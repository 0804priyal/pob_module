<?php

namespace Chilliapple\Redirect\Observer;

class RedirectLink implements \Magento\Framework\Event\ObserverInterface
{

    protected $registry;

    protected $helper;

    protected $redirect;

    protected $redirectCollectionFactory;

    protected $url;

    protected $request;

    protected $collection = null;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Chilliapple\Redirect\Helper\Data $helper,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Chilliapple\Redirect\Model\ResourceModel\Redirect\CollectionFactory $redirectCollectionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->registry = $registry;
        $this->helper   = $helper;
        $this->redirect = $redirect;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
        $this->url      = $url;
        $this->request  = $request;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{

        $collection = $this->getRedirectCollection();

        $urlKey = trim($this->request->getOriginalPathInfo(), '/');

        if(!empty($urlKey)){

            $collection->addSourceUrlFilter($urlKey);

            if(count($collection)){

                $item = $collection->getFirstItem();
                $destUrl = $item->getDestUrl();
                $redirectUrl = $this->url->getUrl($destUrl);
                if($destUrl) {
                $code = $item->getCode() ? $item->getCode() : '302';
                $observer->getControllerAction()->getResponse()->setRedirect($redirectUrl, $code)->sendResponse();
                die();
                }
            }
        }

		return $this;
	}

    protected function getRedirectCollection(){

        if($this->collection === null){

            $this->collection = $this->redirectCollectionFactory->create();
        }

        return $this->collection;
    }

}
