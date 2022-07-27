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
namespace Chilliapple\ProductAttributes\Model\ResourceModel;

class EncyclopediaProducts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Event Manager
     * 
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    protected $helper;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param mixed $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Chilliapple\ProductAttributes\Helper\Data $helper,
        $connectionName = null
    ) {
        $this->eventManager = $eventManager;
        $this->helper = $helper;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('chilliapple_encyclopedia_products', 'entity_id');
    }

    public function getEntityIds($object)
    {
        $tbl = $this->getMainTable();

        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($tbl, 'entity_id')
            ->where('product_id = :product_id');
        $binds = ['product_id' => (int)$object->getId()];
        return $adapter->fetchCol($select, $binds);
    }

    public function saveEncyclopediaProductsAttribute($product){

        $insertData = null;
        $attributes = null;

        $tbl = $this->getMainTable();

        $customAttributes = $product->getCustomAttributesFieldset();

        if(isset($customAttributes['enc_related_product']) && !empty($customAttributes['enc_related_product'])){

            $attributes = $customAttributes['enc_related_product'];

        }

        //remove old
        if($product->getId()){

            $where = [
                'product_id IN (?)' => $product->getId()
            ];
            $this->getConnection()->delete(
                $tbl,
                $where
            );
        }


        //insert new
        if($attributes){
            foreach($attributes as $attribute){
                $insertRow = []; 
                $insertRow['enc_related_product'] = $attribute['enc_related_product']; 
                $insertRow['product_id']     = $product->getId(); 
                $insertRow['sort_order']     = $attribute['sort_order']; 
                $insertData[] = $insertRow;
            }
        }

        if($insertData){

            $this->getConnection()->insertOnDuplicate($tbl, $insertData);
        }

        return $this;
    }

}
