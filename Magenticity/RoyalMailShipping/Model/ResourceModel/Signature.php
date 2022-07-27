<?php

namespace Magenticity\RoyalMailShipping\Model\ResourceModel;

class Signature extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenticity_royalmailshipping_signature', 'id');
    }
}
