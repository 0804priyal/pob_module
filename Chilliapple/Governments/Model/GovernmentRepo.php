<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\Data\GovernmentInterfaceFactory;
use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\Model\ResourceModel\Government as GovernmentResourceModel;

class GovernmentRepo implements GovernmentRepositoryInterface
{
    /**
     * @var GovernmentInterfaceFactory
     */
    private $factory;
    /**
     * @var GovernmentResourceModel
     */
    private $resource;
    /**
     * @var GovernmentInterface[]
     */
    private $cache = [];

    /**
     *
     */
    public function __construct(
        GovernmentInterfaceFactory $factory,
        GovernmentResourceModel $resource
    ) {
        $this->factory = $factory;
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function save(GovernmentInterface $government)
    {
        try {
            $this->resource->save($government);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __($exception->getMessage())
            );
        }
        return $government;
    }

    /**
     * @inheritdoc
     */
    public function get(int $governmentId)
    {
        if (!isset($this->cache[$governmentId])) {
            $government = $this->factory->create();
            $this->resource->load($government, $governmentId);
            if (!$government->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The Government with the ID "%1" does not exist . ', $governmentId)
                );
            }
            $this->cache[$governmentId] = $government;
        }
        return $this->cache[$governmentId];
    }

    /**
     * @inheritdoc
     */
    public function delete(GovernmentInterface $government)
    {
        try {
            $id = $government->getId();
            $this->resource->delete($government);
            unset($this->cache[$id]);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __($exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $governmentId)
    {
        return $this->delete($this->get($governmentId));
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        return $this->cache = [];
    }
}
