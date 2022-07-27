<?php

namespace Magenticity\RoyalMailShipping\Model;

class ServiceIntegration extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    protected function _construct() {
        $this->_init(\Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration::class);
    }
}
