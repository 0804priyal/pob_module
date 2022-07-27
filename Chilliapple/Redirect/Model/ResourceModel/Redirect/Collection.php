<?php
namespace Chilliapple\Redirect\Model\ResourceModel\Redirect;

use Chilliapple\Redirect\Model\Redirect;
use Chilliapple\Redirect\Model\ResourceModel\AbstractCollection;

/**
 * @api
 */
class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Redirect::class,
            \Chilliapple\Redirect\Model\ResourceModel\Redirect::class
        );
    }

    public function addSourceUrlFilter($sourceUrl){

        $this->addFieldToFilter('main_table.source_url', ['eq' => $sourceUrl]);

        return $this;
    }
}
