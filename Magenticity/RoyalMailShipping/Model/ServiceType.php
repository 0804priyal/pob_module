<?php

namespace Magenticity\RoyalMailShipping\Model;

class ServiceType extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    protected function _construct() {
        $this->_init(\Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType::class);
    }
}
