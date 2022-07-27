<?php

namespace Magenticity\RoyalMailShipping\Model;

class ServiceContainer extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceContainer $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceContainer\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    protected function _construct() {
        $this->_init(\Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceContainer::class);
    }
}
