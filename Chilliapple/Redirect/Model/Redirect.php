<?php
namespace Chilliapple\Redirect\Model;

use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Magento\Framework\Model\AbstractModel;
use Chilliapple\Redirect\Model\ResourceModel\Redirect as RedirectResourceModel;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * @method \Chilliapple\Redirect\Model\ResourceModel\Redirect _getResource()
 * @method \Chilliapple\Redirect\Model\ResourceModel\Redirect getResource()
 */
class Redirect extends AbstractModel implements RedirectInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'chilliapple_redirect_redirect';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'chilliapple_redirect_redirect';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'redirect';
    /**
     * Uploader pool
     *
     * @var UploaderPool
     */
    protected $uploaderPool;

    protected $csvReader;

    protected $redirectType;

    /**
     * constructor
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param AbstractResource $resource
     * @param DbCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        \Chilliapple\Redirect\Model\Csv\CsvReader $csvReader,
        \Chilliapple\Redirect\Source\RedirectType $redirectType,
        AbstractResource $resource = null,
        DbCollection $resourceCollection = null,
        array $data = []
    ) {
        $this->csvReader = $csvReader;
        $this->redirectType = $redirectType;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RedirectResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Page id
     *
     * @return array
     */
    public function getRedirectId()
    {
        return $this->getData(RedirectInterface::REDIRECT_ID);
    }

    /**
     * set Redirect id
     *
     * @param  int $redirectId
     * @return RedirectInterface
     */
    public function setRedirectId($redirectId)
    {
        return $this->setData(RedirectInterface::REDIRECT_ID, $redirectId);
    }

    /**
     * @param string $sourceUrl
     * @return RedirectInterface
     */
    public function setSourceUrl($sourceUrl)
    {
        return $this->setData(RedirectInterface::SOURCE_URL, $sourceUrl);
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->getData(RedirectInterface::SOURCE_URL);
    }

    /**
     * @param string $destUrl
     * @return RedirectInterface
     */
    public function setDestUrl($destUrl)
    {
        return $this->setData(RedirectInterface::DEST_URL, $destUrl);
    }

    /**
     * @return string
     */
    public function getDestUrl()
    {
        return $this->getData(RedirectInterface::DEST_URL);
    }

    /**
     * @param string $code
     * @return RedirectInterface
     */
    public function setCode($code)
    {
        return $this->setData(RedirectInterface::CODE, $code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(RedirectInterface::CODE);
    }

    /**
     * @param string $file
     * @return RedirectInterface
     */
    public function setFile($file)
    {
        return $this->setData(RedirectInterface::FILE, $file);
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->getData(RedirectInterface::FILE);
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getFileUrl()
    {
        $url = false;
        $file = $this->getFile();
        if ($file) {
            if (is_string($file)) {
                $uploader = $this->uploaderPool->getUploader('file');
                $url = $uploader->getBaseUrl() . $uploader->getBasePath() . $file;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the File url.')
                );
            }
        }
        return $url;
    }

    public function deleteByOldSourceUrlIfExists($data){

        return $this->getResource()->deleteByOldSourceUrlIfExists($data);
    }

    public function uploadCsvData($file){

        $count = 0;
        $this->csvReader->readCsv($file);
        $csv = $this->csvReader->getRows($keepHeader = false, $combineHeader = true);
        $this->validateCsv($csv);

        $count = $this->getResource()->uploadCsvData($csv);

        return $count;
    }

    protected function validateCsv($csv){

        $error = false;

        if(count($csv)){

            $i = 1;
            foreach($csv as $item){

                $url = trim($item['source_url']);
                if(!isset($url) || empty($url)){

                    throw new LocalizedException(
                        __('Invalid csv, source url at line %1.', $i)
                    );
                }

                $url = trim($item['dest_url']);
                if(!isset($url) || empty($url)){

                    throw new LocalizedException(
                        __('Invalid csv, destination url at line %1.', $i)
                    );
                }

                $code = trim($item['code']);
                if(!in_array($code, $this->redirectType->getAllValues())){
                    
                    throw new LocalizedException(
                        __('Invalid csv, redirect type code at line %1.', $i)
                    );
                }

                $i++;
            }

        }else{
            throw new LocalizedException(
                __('Invalid csv.')
            );
        }


        return true;
    }
}
