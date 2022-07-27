<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class RoyalCreateShipment extends \Magento\Framework\App\Action\Action
{
    protected $_coreRegistry;
    protected $orderManager;
    protected $convertOrder;
    protected $trackFactory;
    protected $resourceConnection;
    protected $_downloader;
    protected $shipmentManager;
    protected $_filesystem;
    protected $shipmentNotifier;
    protected $resultPageFactory;

     public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order $orderManager,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->orderManager = $orderManager;
        $this->convertorder = $convertOrder;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $createShipmentParam = $this->getRequest()->getParam('createshipment');
        $printLabelParam = $this->getRequest()->getParam('shipmentlabel');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $notifyCustomer = $this->getRequest()->getParam('send_email');
        $shipmentDetail = $this->getRequest()->getParam('shipment_detail');
        if ($shipmentId) {
            $this->_coreRegistry->register('shipment_id', $shipmentId);
        }
        if ($orderId) {
            $this->_coreRegistry->register('order_id', $orderId);
        }
        if ($createShipmentParam) {
            $this->_coreRegistry->register('create_shipment_param', $createShipmentParam);
        }
        if ($printLabelParam) {
            $this->_coreRegistry->register('print_label_param', $printLabelParam);
        }
        if ($notifyCustomer) {
            $this->_coreRegistry->register('notify_customer', $notifyCustomer);
        }
        if ($shipmentDetail) {
            $this->_coreRegistry->register('shipment_detail', $shipmentDetail);
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend('Ship order(s) with Royal Mail.');
        return $resultPage;
    }
}
