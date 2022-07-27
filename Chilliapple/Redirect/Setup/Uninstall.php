<?php
namespace Chilliapple\Redirect\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Uninstall constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //remove ui bookmark data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('ui_bookmark'),
            [
                'namespace IN (?)' => [
                    'chilliapple_redirect_redirect_listing',
                ]
            ]
        );
        if ($setup->tableExists('chilliapple_redirect_redirect')) {
            $setup->getConnection()->dropTable('chilliapple_redirect_redirect');
        }
    }
}
