<?php
/**
 * Chilliapple_ProductAttributes extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Chilliapple
 * @package   Chilliapple_ProductAttributes
 * @copyright Copyright (c) 2020
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Chilliapple\ProductAttributes\Model\ResourceModel\EncyclopediaProducts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name
     * 
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'chilliapple_encyclopediaproducts_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'encyclopediaproducts_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Chilliapple\ProductAttributes\Model\EncyclopediaProducts::class,
            \Chilliapple\ProductAttributes\Model\ResourceModel\EncyclopediaProducts::class
        );
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'product_id', $labelField = 'enc_related_product', $additional = ['sort_order'])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return \Magento\Framework\DB\Select
     */
    protected function getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }

    public function addProductFilter($product){

        if($product instanceof \Magento\Catalog\Model\Product){
            $id = $product->getId();
        }else{
            $id = $product;
        }

        $this->addFieldToFilter('product_id', ['eq'=> $id]);

        return $this;
    }
}
