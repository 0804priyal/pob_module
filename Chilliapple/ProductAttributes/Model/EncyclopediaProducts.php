<?php
/**
 * Chilliapple_ProductAttributes extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Chilliapple
 * @package   Chilliapple_ProductAttributes
 * @copyright Copyright (c) 2020
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Chilliapple\ProductAttributes\Model;

class EncyclopediaProducts extends \Magento\Framework\Model\AbstractModel
{

    protected $helper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param DocumentRoot|null $documentRoot
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Chilliapple\ProductAttributes\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Chilliapple\ProductAttributes\Model\ResourceModel\EncyclopediaProducts::class);
    }
    
    public function saveEncyclopediaProductsAttribute($product){

        return $this->getResource()->saveEncyclopediaProductsAttribute($product);

    }

    public function getEncRelatedProductAttributeByProduct($product){

        return $this->getCollection()->addProductFilter($product);

    }
}
