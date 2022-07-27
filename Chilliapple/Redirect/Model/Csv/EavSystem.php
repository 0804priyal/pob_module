<?php
/**
 * Jerrys_Base extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Jerrys
 * @package   Jerrys_Base
 * @copyright Copyright (c) 2019
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Jerrys\Base\Model\Csv;

class EavSystem 
{
	protected $setup = null;

	protected $eavFactory = null;

	protected $eavSetup = null;
	
	protected $connection = null;

	protected $logger = null;

	protected $setColFactory = null;

	protected $optionIds = [];

	protected $entityTypeId = 4;

	protected $attributeSetFactory;

	protected $attributeSets = [];

	protected $productIds = [];

	protected $productRowIds = [];

	public function __construct(
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Eav\Setup\EavSetupFactory $eavFactory,
		\Magento\Framework\Setup\ModuleDataSetupInterface $setup,
		\Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
	){
		$this->setColFactory = $setColFactory;
		$this->logger = $logger;
		$this->eavFactory = $eavFactory;
		$this->setup = $setup;
		$this->attributeSetFactory = $attributeSetFactory;
		$this->init();
	}

	protected function init()
	{

		$this->connection = $this->setup->getConnection();

		$this->eavSetup = $this->eavFactory->create(['setup' => $this->setup]);

		$this->optionTable = $this->setup->getTable('eav_attribute_option');

		$this->valueTable = $this->setup->getTable('eav_attribute_option_value');
	}

	public function getAttributeFromLabel($label,$availableAttributes)
	{	
		$label = trim($label);

		$eavAttributeTable = $this->setup->getTable('eav_attribute');

		$select = $this->connection->select()->from($eavAttributeTable,['*'])
					->where('entity_type_id=?', "4")
					->where('backend_type=?', $availableAttributes['type'])
					->where('frontend_input=?', $availableAttributes['input'])
					->where('frontend_label=?', $label)
					;

		return $this->setup->getConnection()->fetchRow($select);
	}

	public function getAttributeSetIdByName($setName){

		if(!isset($this->attributeSets[$setName])){

	        $entityTypeId = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
	        $attributeSetCollection = $this->attributeSetFactory->create()->getCollection()
	                ->addFieldToSelect('attribute_set_id')
	                ->addFieldToFilter('entity_type_id', ['eq' => $entityTypeId])
	                ->addFieldToFilter('attribute_set_name', ['eq' => $setName]);

	         if(count($attributeSetCollection)){

	         	$attributeSetCollection = $attributeSetCollection->getFirstItem()->toArray();
	         	$this->attributeSets[$setName] = (int)$attributeSetCollection['attribute_set_id'];

	         }else{
	         	$this->attributeSets[$setName] = false;
	         }
		}

        return $this->attributeSets[$setName];
	}

	public function getProductIdBySku($sku)
	{	

		if(!isset($this->productIds[$sku])){

			$productEntity = $this->setup->getTable('catalog_product_entity');
			$select = $this->connection->select()->from($productEntity,['entity_id'])
						->where('sku=?', $sku);

			$productId = $this->setup->getConnection()->fetchOne($select);
			$this->productIds[$sku] = $productId;
		}
					
		return $this->productIds[$sku];
	}

	public function getOptionIdsCache(){

		return $this->optionIds;
	}
}