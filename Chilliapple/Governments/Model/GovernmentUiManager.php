<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\Ui\EntityUiManagerInterface;

class GovernmentUiManager implements EntityUiManagerInterface
{
    /**
     * @var GovernmentRepositoryInterface
     */
    private $repository;
    /**
     * @var GovernmentFactory
     */
    public $factory;

    /**
     * @param GovernmentRepositoryInterface $repository
     * @param GovernmentFactory $factory
     */
    public function __construct(
        GovernmentRepositoryInterface $repository,
        GovernmentFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param int|null $id
     * @return \Magento\Framework\Model\AbstractModel | Government | GovernmentInterface;
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(?int $id)
    {
        return ($id)
            ? $this->repository->get($id)
            : $this->factory->create();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $government
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Framework\Model\AbstractModel $government)
    {
        $this->repository->save($government);
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(int $id)
    {
        $this->repository->deleteById($id);
    }
}
