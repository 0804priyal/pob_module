<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model\ResourceModel;

use Chilliapple\Governments\Model\ResourceModel\Store;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    /**
     * @var AbstractCollection | MockObject
     */
    private $collection;
    /**
     * @var Store
     */
    private $store;
    /**
     * @var AdapterInterface | MockObject
     */
    private $connection;
    /**
     * @var Select | MockObject
     */
    private $select;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $om = new ObjectManager($this);
        $this->collection = $om->getCollectionMock(
            AbstractCollection::class,
            [
                $this->getItemMock('1'),
                $this->getItemMock('2'),
                $this->getItemMock('3')
            ]
        );
        $this->connection = $this->createMock(AdapterInterface::class);
        $this->collection->method('getConnection')->willReturn($this->connection);
        $this->select = $this->createMock(Select::class);
        $this->store = new Store();
    }

    /**
     * @covers \Chilliapple\Governments\Model\ResourceModel\Store::addStoresToCollection
     */
    public function testAddStoresToCollection()
    {
        $this->collection->method('getColumnValues')->willReturn([1, 2, 3]);
        $this->connection->method('select')->willReturn($this->select);
        $this->select->expects($this->once())->method('from')->willReturnSelf();
        $this->select->expects($this->once())->method('where')->willReturnSelf();
        $this->connection->method('fetchAll')->willReturn([
            [
                'store_id' => 1,
                'linked_id' => 1
            ],
            [
                'store_id' => 2,
                'linked_id' => 1
            ],
            [
                'store_id' => 2,
                'linked_id' => 2
            ],
        ]);
        $this->store->addStoresToCollection($this->collection, 'table', 'linked_id');
    }

    /**
     * @covers \Chilliapple\Governments\Model\ResourceModel\Store::addStoreFilter
     */
    public function testAddStoreFilter()
    {
        $this->collection->expects($this->once())->method('addFilter')->with('store_id', ['in' => [1, 0]], 'public');
        $this->store->addStoreFilter($this->collection, 1);
    }

    /**
     * @covers \Chilliapple\Governments\Model\ResourceModel\Store::joinStoreRelationTable
     */
    public function testJoinStoreRelationTable()
    {
        $this->collection->method('getSelect')->willReturn($this->select);
        $this->select->expects($this->once())->method('join')->willReturnSelf();
        $this->select->expects($this->once())->method('group');
        $this->store->joinStoreRelationTable($this->collection, 'table', 'link_id');
    }

    /**
     * @param $linkedField
     * @return MockObject
     */
    private function getItemMock($linkedField)
    {
        $mock = $this->createMock(AbstractModel::class);
        $mock->method('getData')->willReturn($linkedField);
        return $mock;
    }
}
