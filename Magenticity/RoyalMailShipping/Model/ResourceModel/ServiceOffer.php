<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel;

class ServiceOffer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenticity_royalmailshipping', 'id');
    }
}
