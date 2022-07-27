<?php

namespace Chilliapple\ProductAttributes\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockState;

    protected $sourceData;

    protected $timezone;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku $sourceData,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->stockState = $stockState;
        $this->sourceData = $sourceData;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaetgoryId($field)
    {
    	$configPath = "catalog/product_attributes/".$field;
    	return $this->getConfig($configPath);
    }

    /**
     * Retrieve stock qty whether product
     *
     * @param int $productId
     * @param int $websiteId
     * @return float
     */
    public function getStockQty($product, $websiteId = null)
    {   $productId = $product->getId();
        return $this->stockState->getStockQty($productId, $websiteId);
    }

    public function getStockQtyBySku($product, $websiteId = null)
    {   
        $websites = [];
        $websites['0'] = "default";
        $websites['1'] = "default";
        $websites['2'] = "us";
        $sourceCode = isset($websites[$websiteId]) ? $websites[$websiteId] : 'default';
        $sources = $this->sourceData->execute($product->getSku());
        foreach($sources as $source)
        {
            if($source['source_code'] == $sourceCode)
            {
                return $source;
            }
        }
    }

    public function getStockStatus($product, $websiteId = null)
    {
        $status = [];
        $qty = null;
        $source = $this->getStockQtyBySku($product, $websiteId);
        if($source){
         $qty = $source['quantity'];
        }
        if($qty == -1000)
        {
            $status['label'] = __("Sold out");
            $status['css_class'] = 'product-label out-stock-label';
            $status['qty'] = $qty;
        }

        if($qty == 0)
        {
            $status['label'] = __("Out of stock");
            $status['css_class'] = 'product-label out-stock-label';
            $status['qty'] = $qty;
        }

        if($qty > 0 && $qty <=5)
        {
            $status['label'] = __("Low stock");
            $status['css_class'] = 'product-label low-stock-label';
            $status['qty'] = $qty;
        }
        return $status;
    }

    public function getTimeZone()
    {
        return $this->timezone;
    }
}
