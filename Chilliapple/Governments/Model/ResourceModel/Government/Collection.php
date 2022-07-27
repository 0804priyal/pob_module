<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model\ResourceModel\Government;

use Chilliapple\Governments\Model\ResourceModel\Collection\StoreAwareAbstractCollection;

/**
 * @api
 */
class Collection extends StoreAwareAbstractCollection
{
    /**
     * @var string
     * phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore,PSR12.Classes.PropertyDeclaration.Underscore
     */
    protected $_idFieldName = 'government_id';
    //phpcs: enable

    /**
     * Define resource model
     *
     * @return void
     * @codeCoverageIgnore
     * //phpcs:disable PSR2.Methods.MethodDeclaration.Underscore,PSR12.Methods.MethodDeclaration.Underscore
     */
    protected function _construct()
    {
        $this->_init(
            \Chilliapple\Governments\Model\Government::class,
            \Chilliapple\Governments\Model\ResourceModel\Government::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
        $this->_map['fields']['government_id'] = 'main_table.government_id';
        //phpcs: enable
    }
}
