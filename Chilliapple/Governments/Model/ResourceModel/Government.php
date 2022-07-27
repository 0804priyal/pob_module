<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model\ResourceModel;

use Chilliapple\Governments\Model\ResourceModel\StoreAwareAbstractModel;

class Government extends StoreAwareAbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     * @codeCoverageIgnore
     * //phpcs:disable PSR2.Methods.MethodDeclaration.Underscore,PSR12.Methods.MethodDeclaration.Underscore
     */
    protected function _construct()
    {
        $this->_init('governments_government', 'government_id');
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @codeCoverageIgnore
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->where('is_active = ?', 1);
        return $select;
    }
    //phpcs: enable
}
