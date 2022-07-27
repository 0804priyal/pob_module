<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceEnhancement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenticity\RoyalMailShipping\Model\ServiceEnhancement', 'Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceEnhancement');
    }
}
