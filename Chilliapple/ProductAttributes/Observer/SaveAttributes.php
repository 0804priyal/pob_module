<?php

namespace Chilliapple\ProductAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SaveAttributes implements ObserverInterface
{ 

    protected $request;

    protected $helper;

    protected $coinHighlights;

    protected $cncyclopediaProducts;

    public function __construct(
        RequestInterface $request,
        \Chilliapple\ProductAttributes\Helper\Data $helper,
        \Chilliapple\ProductAttributes\Model\CoinHighlights $coinHighlights,
        \Chilliapple\ProductAttributes\Model\EncyclopediaProducts $cncyclopediaProducts

    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->coinHighlights = $coinHighlights;
        $this->cncyclopediaProducts = $cncyclopediaProducts;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();

        $action = $this->request->getFullActionName();


        if($action == 'catalog_product_save'){

            $this->coinHighlights->saveCoinHighlightsAttribute($product);
            $this->cncyclopediaProducts->saveEncyclopediaProductsAttribute($product);
        }
        

        return $this;
    }   
}
