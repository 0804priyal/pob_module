<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel\Signature;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenticity\RoyalMailShipping\Model\Signature', 'Magenticity\RoyalMailShipping\Model\ResourceModel\Signature');
    }
}
