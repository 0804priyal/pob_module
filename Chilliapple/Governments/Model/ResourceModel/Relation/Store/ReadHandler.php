<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model\ResourceModel\Relation\Store;

use Chilliapple\Governments\Model\ResourceModel\StoreAwareAbstractModel;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var StoreAwareAbstractModel
     */
    private $resource;
    /**
     * @var string
     */
    private $storeIdField;

    /**
     * ReadHandler constructor.
     * @param StoreAwareAbstractModel $resource
     * @param string $storeIdField
     */
    public function __construct(StoreAwareAbstractModel $resource, string $storeIdField = 'store_id')
    {
        $this->resource = $resource;
        $this->storeIdField = $storeIdField;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return bool|object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resource->lookupStoreIds((int)$entity->getId());
            $entity->setData($this->storeIdField, $stores);
        }
        return $entity;
    }
}
