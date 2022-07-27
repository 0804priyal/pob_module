<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenticity\RoyalMailShipping\Model\ServiceType', 'Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType');
    }
}
