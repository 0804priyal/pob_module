<?php

namespace Magenticity\RoyalMailShipping\Model;

class Signature extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\Signature $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\Signature\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    protected function _construct() {
        $this->_init(\Magenticity\RoyalMailShipping\Model\ResourceModel\Signature::class);
    }
}
