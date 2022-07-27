<?php

namespace Magenticity\RoyalMailShipping\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class ServiceEnhancement implements ArrayInterface
{
    protected $resource;
    protected $developConfig;

    public function __construct(
    \Magento\Framework\App\ResourceConnection $resource,
    \Magento\Framework\App\DeploymentConfig $developConfig
    ) {
        $this->resource = $resource;
        $this->developConfig = $developConfig;
    }

    public function toOptionArray() {
        $options = [];
        if (!empty($this->getServiceEnhancementList())) {
            foreach ($this->getServiceEnhancementList() as $field) {
                $options[] = ['label' => $field['enhancement_desc'], 'value' => $field['enhancement_type']];
            }
        }
        return $options;
    }
    public function getServiceEnhancementList() {
        $connection = $this->resource->getConnection();
        $tablePrefix = $this->developConfig->get('db/table_prefix');
        $ServiceEnhanceTable = "";
        if ($tablePrefix) {
            $ServiceEnhanceTable = $tablePrefix."magenticity_royalmailshipping_serviceenhancement";
        } else {
            $ServiceEnhanceTable = "magenticity_royalmailshipping_serviceenhancement";
        }
        $ServiceEnhancement = $connection->fetchAll("SELECT enhancement_type,enhancement_desc,enhancement_group FROM {$ServiceEnhanceTable}");
        return $ServiceEnhancement;
    }
}
