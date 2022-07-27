<?php

namespace Magenticity\RoyalMailShipping\Plugin\Adminhtml\Order\Tracking;

class View
{
    public function aroundGetCarrierTitle(\Magento\Shipping\Block\Adminhtml\Order\Tracking\View $subject, callable $proceed, $Code) {
    	if ($Code == "RoyalMail") {
    		return __('RoyalMail');
    	} else {
    		$result = $proceed($Code);
            return $result;
        }
    }
}
