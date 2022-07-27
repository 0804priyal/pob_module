<?php

namespace Magenticity\RoyalMailShipping\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class SignatureOffer implements ArrayInterface
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
        if (!empty($this->getSignatureOfferingList())) {
            foreach ($this->getSignatureOfferingList() as $field) {
                $options[] = ['label' => $field['signature_desc'], 'value' => $field['signature_code']];
            }
        }
        return $options;
    }
    public function getSignatureOfferingList() {
        $connection = $this->resource->getConnection();
        $tablePrefix = $this->developConfig->get('db/table_prefix');
        if ($tablePrefix) {
            $signaturetable = $tablePrefix."magenticity_royalmailshipping_signature";
        } else {
            $signaturetable = "magenticity_royalmailshipping_signature";
        }
        $signaturedetails = $connection->fetchAll("SELECT signature_code,signature_desc FROM {$signaturetable}");
        return $signaturedetails;
    }
}
