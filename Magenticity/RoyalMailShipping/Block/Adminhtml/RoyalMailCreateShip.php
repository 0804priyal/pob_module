<?php

namespace Magenticity\RoyalMailShipping\Block\Adminhtml;

class RoyalMailCreateShip extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'order/royalship.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magenticity\RoyalMailShipping\Helper\Data $dataHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->_coreRegistry = $coreRegistry;
        $this->dataHelper = $dataHelper;
        $this->_orderFactory = $orderFactory;
        $this->formKey = $context->getFormKey();
    }

    public function getOrderId() {
        return $this->_coreRegistry->registry('order_id');
    }

    public function getShipmentId() {
        return $this->_coreRegistry->registry('shipment_id');
    }

    public function getNotifyCustomer() {
        return $this->_coreRegistry->registry('notify_customer');
    }

    public function IsShipmentDetails() {
        return $this->_coreRegistry->registry('shipment_detail');
    }

    public function getShipmentFlag() {
        $flag = "";
        $createShipmentParam = $this->_coreRegistry->registry('create_shipment_param');
        if (!empty($createShipmentParam)) {
            $flag = "shipmentonly";
        }
        $printLabelParam = $this->_coreRegistry->registry('print_label_param');
        if (!empty($printLabelParam)) {
            $flag = "shipmentwithprintlabel";
        }
        return $flag;
    }

    public function getOrderNumber() {
        $EntityId = $this->getOrderId();
        $order = $this->_orderFactory->create()->load($EntityId);
        $orderNumber = $order->getIncrementId();
        return $orderNumber;
    }

    public function getServiceType() {
        $serviceType = $this->dataHelper->serviceType();
        return $serviceType;
    }

    public function getServiceOffering() {
        $serviceOffering = $this->dataHelper->getServiceOfferConfig();
        $finalserviceOffering = explode(",", $serviceOffering);
        return $finalserviceOffering;
    }

    public function getServiceFormat() {
        $serviceFormat = $this->dataHelper->getServiceFormatConfig();
        $finalserviceFormat = explode(",", $serviceFormat);
        return $finalserviceFormat;
    }

    public function getServiceEnhancement() {
        $serviceEnhancement = $this->dataHelper->ServiceEnhancementType();
        $finalserviceEnhancement = explode(",", $serviceEnhancement);
        return $finalserviceEnhancement;
    }

    public function getFormKey() {
        return $this->formKey->getFormKey();
    }

    public function getRoyalmailTrackingNumber($entityid) {
        $OrderEntityId = $entityid;
        $order = $this->_orderFactory->create()->load($OrderEntityId);
        $state = $order->getState();
        $RoyalTrackingNumber = array();
        if ($order->getShipmentsCollection()) {
            $ShipmentCollection = $order->getShipmentsCollection();
            if (!empty($ShipmentCollection)) {
                foreach ($ShipmentCollection as $shipmentData) {
                    $RoyalTrackingNumber['royal_tracking_number'] = $shipmentData->getRmTrackingNumber();
                    $RoyalTrackingNumber['cancel_shipment'] = $shipmentData->getCancelRoyalShipment();
                }
                $CountShipment = count($ShipmentCollection);
                $RoyalTrackingNumber['order_state'] = $state;
                $RoyalTrackingNumber['shipment_count'] = $CountShipment;
                $CanShip = "";
                if ($order->hasShipments() && $order->canShip()) {
                    $CanShip = "1";
                }
                $RoyalTrackingNumber['canship'] = $CanShip;
                return $RoyalTrackingNumber;
            }
        }
    }

    public function CheckCancelShipment($entityid) {
        $OrderEntityId = $entityid;
        $order = $this->_orderFactory->create()->load($OrderEntityId);
        $cancelShipment = "";
        if ($order->getShipmentsCollection()) {
            $ShipmentCollection = $order->getShipmentsCollection();
            if (!empty($ShipmentCollection)) {
                foreach ($ShipmentCollection as $shipmentData) {
                    $cancelShipment = $shipmentData->getCancelRoyalShipment();
                }
                return $cancelShipment;
            }
        }
    }

    public function IsEnhancementType() {
        $checkEnhancementType = $this->dataHelper->IsServiceEnhancement();
        return $checkEnhancementType;
    }

    public function getOrderDetailUrl() {
        $OrderDetailUrl = $this->_urlBuilder->getUrl('sales/order/view', ['order_id' => $this->getOrderId()]);
        return $OrderDetailUrl;
    }
}
