<?php

namespace Chilliapple\ProductAttributes\Block\Product\View;


class CoinHighlights extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_collectionHighlightsFactory = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Chilliapple\ProductAttributes\Model\ResourceModel\CoinHighlights\CollectionFactory $collectionHighlightsFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_collectionHighlightsFactory = $collectionHighlightsFactory;
        parent::__construct($context, $data);
    }

    public function getHighlights()
    {
        $collectionHighlights = $this->_collectionHighlightsFactory->create();
        $collectionHighlights->addFieldToFilter("product_id", $this->getProduct()->getId())->setOrder("sort_order", "ASC");
        return $collectionHighlights;
    }
    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
}

