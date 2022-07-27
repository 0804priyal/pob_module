<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel\Government;

use Chilliapple\Governments\Model\ResourceModel\Government\Collection;
use Chilliapple\Governments\Model\ResourceModel\Government\CollectionFactory;
use Chilliapple\Governments\ViewModel\Government\ListGovernment;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Pager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListGovernmentTest extends TestCase
{
    /**
     * @var StoreManagerInterface | MockObject
     */
    private $storeManager;
    /**
     * @var CollectionFactory | MockObject
     */
    private $collectionFactory;
    /**
     * @var BlockFactory | MockObject
     */
    private $blockFactory;
    /**
     * @var Collection
     */
    private $collection;
    /**
     * @var ListGovernment
     */
    private $listGovernment;
    /**
     * @var Pager | MockObject
     */
    private $pager;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->storeManager->method('getStore')->willReturn($this->createMock(StoreInterface::class));
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->blockFactory = $this->createMock(BlockFactory::class);
        $this->collection = $this->createMock(Collection::class);
        $this->pager = $this->createMock(Pager::class);
        $this->listGovernment = new ListGovernment(
            $this->storeManager,
            $this->collectionFactory,
            $this->blockFactory
        );
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getCollection
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::processCollection
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getHash
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetGovernmentCollection()
    {
        $this->listGovernment->setFilters([
            [
                'field' => 'field',
                'condition' => 'condition'
            ],
            [
                'field' => 'field'
            ],
            []
        ]);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($this->collection);
        $this->collection->expects($this->once())->method('addStoreFilter');
        $this->collection->expects($this->exactly(2))->method('addFieldToFilter');
        $this->blockFactory->expects($this->once())->method('createBlock')->willReturn($this->pager);
        $this->assertEquals($this->collection, $this->listGovernment->getCollection());
        //call twice to test memoizing
        $this->assertEquals($this->collection, $this->listGovernment->getCollection());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getPagerHtml
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::processCollection
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getHash
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetPagerHtml()
    {
        $this->pager->method('toHtml')->willReturn('pager_html');
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($this->collection);
        $this->blockFactory->expects($this->once())->method('createBlock')->willReturn($this->pager);
        $this->assertEquals('pager_html', $this->listGovernment->getPagerHtml());
        //call twice to test memoizing
        $this->assertEquals('pager_html', $this->listGovernment->getPagerHtml());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::setLabel
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getLabel
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetLabel()
    {
        $this->assertEquals('', $this->listGovernment->getLabel());
        $this->listGovernment->setLabel('label');
        $this->assertEquals('label', $this->listGovernment->getLabel());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::setPageLimitVarName
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getPageLimitVarName
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetPageLimitVarName()
    {
        $this->assertEquals('', $this->listGovernment->getPageLimitVarName());
        $this->listGovernment->setPageLimitVarName('limit');
        $this->assertEquals('limit', $this->listGovernment->getPageLimitVarName());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::setPageFragment
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getPageFragment
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetPageFragment()
    {
        $this->assertEquals('', $this->listGovernment->getPageFragment());
        $this->listGovernment->setPageFragment('fragment');
        $this->assertEquals('fragment', $this->listGovernment->getPageFragment());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::setPageVarName
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getPageVarName
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetPageVarName()
    {
        $this->assertEquals('', $this->listGovernment->getPageVarName());
        $this->listGovernment->setPageVarName('page');
        $this->assertEquals('page', $this->listGovernment->getPageVarName());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::setFilters
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::getFilters
     * @covers \Chilliapple\Governments\ViewModel\Government\ListGovernment::__construct
     */
    public function testGetFilters()
    {
        $this->assertEquals([], $this->listGovernment->getFilters());
        $this->listGovernment->setFilters([['filter']]);
        $this->assertEquals([['filter']], $this->listGovernment->getFilters());
    }
}
