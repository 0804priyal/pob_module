<?php

namespace Magenticity\RoyalMailShipping\Model;

class ServiceOffer extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceOffer $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceOffer\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    protected function _construct() {
        $this->_init(\Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceOffer::class);
    }
}
