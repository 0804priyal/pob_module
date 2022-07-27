<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Model\ResourceModel\Government as GovernmentResourceModel;
use Magento\Framework\Model\AbstractModel;

class Government extends AbstractModel implements GovernmentInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    public const CACHE_TAG = 'chilliapple_governments_government';
    /**
     * Cache tag
     *
     * @var string
     * phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore,PSR12.Classes.PropertyDeclaration.Underscore
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'chilliapple_governments_government';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'government';
    //phpcs:enable
    /**
     * Initialize resource model
     *
     * @return void
     * phpcs:disable PSR2.Methods.MethodDeclaration.Underscore,PSR12.Methods.MethodDeclaration.Underscore
     */
    protected function _construct()
    {
        $this->_init(GovernmentResourceModel::class);
        //phpcs:enable
    }

    /**
     * Get Government id
     *
     * @return array
     */
    public function getGovernmentId()
    {
        return $this->getData(GovernmentInterface::GOVERNMENT_ID);
    }

    /**
     * set Government id
     *
     * @param  int $governmentId
     * @return GovernmentInterface
     */
    public function setGovernmentId($governmentId)
    {
        return $this->setData(self::GOVERNMENT_ID, $governmentId);
    }

    /**
     * @param string $title
     * @return GovernmentInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $featureImage
     * @return GovernmentInterface
     */
    public function setFeatureImage($featureImage)
    {
        return $this->setData(self::FEATURE_IMAGE, $featureImage);
    }

    /**
     * @return string
     */
    public function getFeatureImage()
    {
        return $this->getData(self::FEATURE_IMAGE);
    }

    /**
     * @param string $description
     * @return GovernmentInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $governmentLink
     * @return GovernmentInterface
     */
    public function setGovernmentLink($governmentLink)
    {
        return $this->setData(self::GOVERNMENT_LINK, $governmentLink);
    }

    /**
     * @return string
     */
    public function getGovernmentLink()
    {
        return $this->getData(self::GOVERNMENT_LINK);
    }

    /**
     * @param array $storeId
     * @return GovernmentInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(GovernmentInterface::STORE_ID, $storeId);
    }

    /**
     * @return int[]
     */
    public function getStoreId()
    {
        return $this->getData(GovernmentInterface::STORE_ID);
    }

    /**
     * @param string $metaTitle
     * @return GovernmentInterface
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(GovernmentInterface::META_TITLE, $metaTitle);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getData(GovernmentInterface::META_TITLE);
    }

    /**
     * @param string $metaDescription
     * @return GovernmentInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(GovernmentInterface::META_DESCRIPTION, $metaDescription);
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getData(GovernmentInterface::META_DESCRIPTION);
    }

    /**
     * @param string $metaKeywords
     * @return GovernmentInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(GovernmentInterface::META_KEYWORDS, $metaKeywords);
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getData(GovernmentInterface::META_KEYWORDS);
    }

    /**
     * @param int $isActive
     * @return GovernmentInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(GovernmentInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(GovernmentInterface::IS_ACTIVE);
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
}
