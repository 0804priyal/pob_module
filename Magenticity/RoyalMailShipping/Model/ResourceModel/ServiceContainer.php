<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel;

class ServiceContainer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenticity_royalmailshipping_serviceformat', 'id');
    }
}
