<?php

namespace Magenticity\RoyalMailShipping\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class AllowedContainer implements ArrayInterface
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
        if (!empty($this->getAllowedContainerList())) {
            foreach ($this->getAllowedContainerList() as $field) {
                $options[] = ['label' => $field['service_format_desc'], 'value' => $field['service_format_code']];
            }
        }
        return $options;
    }
    public function getAllowedContainerList() {
        $connection = $this->resource->getConnection();
        $tablePrefix = $this->developConfig->get('db/table_prefix');
        $ServiceFormatTable = "";
        if ($tablePrefix) {
            $ServiceFormatTable = $tablePrefix."magenticity_royalmailshipping_serviceformat";
        } else {
            $ServiceFormatTable = "magenticity_royalmailshipping_serviceformat";
        }
        $AllowedContainer = $connection->fetchAll("SELECT service_format_code,service_format_desc FROM {$ServiceFormatTable}");
        return $AllowedContainer;
    }
}
