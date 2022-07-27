<?php

namespace Chilliapple\ProductAttributes\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class FilterableAttributes implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributes = [
          ['code' => 'coin_year', 'label'=> 'Coin Year', 'source'=> \Chilliapple\ProductAttributes\Model\Entity\Source\CoinYear::class],
          ['code' => 'coin_government', 'label'=> 'Government', 'source'=> \Chilliapple\ProductAttributes\Model\Entity\Source\Government::class],
          ['code' => 'coin_metal', 'label'=> 'Coin Metal', 'source'=> \Chilliapple\ProductAttributes\Model\Entity\Source\Metal::class],
          ['code' => 'coin_effect', 'label'=> 'Coin Effect', 'source'=> \Chilliapple\ProductAttributes\Model\Entity\Source\Effect::class],
        ];
      foreach($attributes as $attribute) {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attribute['code'],
            [
               'type' => 'int',  /* int|static|text|decimal|datetime */
               'label' => $attribute['label'],
               'input' => 'select',
               'source' => $attribute['source'],
               'required' => false,
               'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
               'visible' => true,
               'user_defined' => true,
               'searchable' => true,
               'filterable' => true,
               'comparable' => true,
               'visible_on_front' => true,
               'unique' => false,
               'apply_to' => 'simple',
               'group' => 'Product Details',
               'is_used_in_grid' => false,
               'is_visible_in_grid' => false,
               'is_filterable_in_grid' => false
            ]
          );
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.0.1';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

}
