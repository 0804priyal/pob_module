<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenticity\RoyalMailShipping\Model\ServiceIntegration', 'Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration');
    }
}
