<?php

namespace Magenticity\RoyalMailShipping\Plugin\Adminhtml\Order;

class Tracking
{
    public function afterGetCarriers(\Magento\Shipping\Block\Adminhtml\Order\Tracking $subject, $result) {
        $result['RoyalMail'] = __('RoyalMail');
        return $result;
    }
}
