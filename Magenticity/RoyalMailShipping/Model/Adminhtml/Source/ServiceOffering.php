<?php

namespace Magenticity\RoyalMailShipping\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class ServiceOffering implements ArrayInterface
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
        if (!empty($this->getServiceOfferingList())) {
            foreach ($this->getServiceOfferingList() as $field) {
                $options[] = ['label' => $field['service_offering_name'], 'value' => $field['service_offering_code']];
            }
        }
        return $options;
    }
    public function getServiceOfferingList() {
        $connection = $this->resource->getConnection();
        $tablePrefix = $this->developConfig->get('db/table_prefix');
        $ServiceOfferingTable = "";
        if ($tablePrefix) {
            $ServiceOfferingTable = $tablePrefix."magenticity_royalmailshipping";
        } else {
            $ServiceOfferingTable = "magenticity_royalmailshipping";
        }
        $offeringdetails = $connection->fetchAll("SELECT service_offering_name,service_offering_code FROM {$ServiceOfferingTable}");
        return $offeringdetails;
    }
}
