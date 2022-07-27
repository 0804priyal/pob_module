<?php

namespace Magenticity\RoyalMailShipping\Block\Adminhtml;

class RoyalMailItems extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    public function getOrder() {
        return $this->getShipment()->getOrder();
    }

    public function getSource() {
        return $this->getShipment();
    }

    public function getShipment() {
        return $this->_coreRegistry->registry('current_shipment');
    }

    public function getOrderId() {
        return $this->getShipment()->getOrderId();
    }
}
