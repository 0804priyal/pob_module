<?php
namespace Chilliapple\Redirect\Controller\Adminhtml\Redirect;

use Chilliapple\Redirect\Api\RedirectRepositoryInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Chilliapple\Redirect\Model\UploaderPool;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Class SaveImport
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveImport extends Action
{
    /**
     * Redirect factory
     * @var RedirectInterfaceFactory
     */
    protected $redirectFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Uploader pool
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Redirect repository
     * @var RedirectRepositoryInterface
     */
    protected $redirectRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param RedirectInterfaceFactory $redirectFactory
     * @param RedirectRepositoryInterface $redirectRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param UploaderPool $uploaderPool
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        RedirectInterfaceFactory $redirectFactory,
        RedirectRepositoryInterface $redirectRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        UploaderPool $uploaderPool,
        Registry $registry,
        File $file

    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirectRepository = $redirectRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->uploaderPool = $uploaderPool;
        $this->file = $file;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var RedirectInterface $redirect */
        $redirect = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;

        $resultRedirect = $this->resultRedirectFactory->create();
        try {

            $redirect = $this->redirectFactory->create();
            $filePath = $this->getTempFilePath($postData);

            if(empty($filePath)){
                throw new LocalizedException(
                    __('Invalid csv.')
                );
            }

            $count = $redirect->uploadCsvData($filePath);
            $this->removeTempFile($filePath);

            $this->messageManager->addSuccessMessage(__('You saved the Redirects with %1 records', $count));
            $this->dataPersistor->clear('chilliapple_redirect_redirect');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/import');
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('chilliapple_redirect_redirect', $postData);
            $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Redirects'));
            $this->dataPersistor->set('chilliapple_redirect_redirect', $postData);
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }

    protected function getTempFilePath($data){

        $file = null;

        if(isset($data['file'][0]['path']) && isset($data['file'][0]['file'])){
            $file = $data['file'][0]['path'].$data['file'][0]['file'];
        }

        return $file;
    }

    protected function removeTempFile($fielPath){

        if ($this->file->isExists($fielPath))  {

            $this->file->deleteFile($fielPath);
        }

        return true;
    }
}
