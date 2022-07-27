<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model;

use Chilliapple\Governments\Model\ResourceModel\Government\CollectionFactory;
use Chilliapple\Governments\Ui\CollectionProviderInterface;

class GovernmentUiCollectionProvider implements CollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $factory;

    /**
     * @param CollectionFactory $factory
     */
    public function __construct(CollectionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->factory->create();
    }
}
