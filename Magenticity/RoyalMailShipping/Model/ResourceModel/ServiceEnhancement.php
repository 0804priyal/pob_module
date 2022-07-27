<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel;

class ServiceEnhancement extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenticity_royalmailshipping_serviceenhancement', 'id');
    }
}
