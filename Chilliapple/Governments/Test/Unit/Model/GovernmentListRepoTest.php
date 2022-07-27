<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Api\Data\GovernmentSearchResultInterface;
use Chilliapple\Governments\Api\Data\GovernmentSearchResultInterfaceFactory;
use Chilliapple\Governments\Model\Government;
use Chilliapple\Governments\Model\GovernmentListRepo;
use Chilliapple\Governments\Model\ResourceModel\Government\Collection;
use Chilliapple\Governments\Model\ResourceModel\Government\CollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GovernmentListRepoTest extends TestCase
{
    /**
     * @var GovernmentSearchResultInterfaceFactory | MockObject
     */
    private $searchResultsFactory;
    /**
     * @var CollectionFactory | MockObject
     */
    private $collectionFactory;
    /**
     * @var SearchCriteriaInterface | MockObject
     */
    private $searchCriteria;
    /**
     * @var FilterGroup | MockObject
     */
    private $filterGroup;
    /**
     * @var Collection | MockObject
     */
    private $collection;
    /**
     * @var GovernmentListRepo
     */
    private $governmentListRepo;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->searchResultsFactory = $this->createMock(GovernmentSearchResultInterfaceFactory::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $this->filterGroup = $this->createMock(FilterGroup::class);
        $this->collection = $this->createMock(Collection::class);
        $this->governmentListRepo = new GovernmentListRepo(
            $this->searchResultsFactory,
            $this->collectionFactory
        );
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentListRepo::getList
     * @covers \Chilliapple\Governments\Model\GovernmentListRepo::addFilterGroupToCollection
     * @covers \Chilliapple\Governments\Model\GovernmentListRepo::__construct
     */
    public function testGetList()
    {
        /** @var SearchCriteriaInterface | MockObject $searchCriteria */
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $searchResults = $this->createMock(GovernmentSearchResultInterface::class);
        $searchResults->expects($this->once())->method('setSearchCriteria');
        $this->searchResultsFactory->method('create')->willReturn($searchResults);

        $searchCriteria->method('getFilterGroups')->willReturn($this->getGroupFiltersMock());
        $searchCriteria->method('getSortOrders')->willReturn($this->getSortOrdersMock());

        $collection = $this->createMock(Collection::class);
        $collection->method('getItems')->willReturn([
            $this->getGovernmentMock(),
            $this->getGovernmentMock(),
        ]);
        $collection->expects($this->once())->method('addStoreFilter');
        $collection->expects($this->once())->method('addFieldToFilter');
        $collection->expects($this->exactly(2))->method('addOrder');
        $this->collectionFactory->method('create')->willReturn($collection);

        $searchResults->expects($this->once())->method('setTotalCount');
        $searchResults->expects($this->once())->method('setItems')->willReturnSelf();

        $this->assertEquals($searchResults, $this->governmentListRepo->getList($searchCriteria));
    }

    /**
     * @return array
     */
    private function getGroupFiltersMock(): array
    {
        $filterGroup = $this->createMock(FilterGroup::class);
        $filter1 = $this->createMock(Filter::class);
        $filter2 = $this->createMock(Filter::class);
        $filter2->method('getField')->willReturn('store');
        $filterGroup->method('getFilters')->willReturn([
            $filter1,
            $filter2
        ]);
        return [$filterGroup];
    }

    /**
     * @return array
     */
    private function getSortOrdersMock(): array
    {
        return [
            $this->createMock(SortOrder::class),
            $this->createMock(SortOrder::class)
        ];
    }

    /**
     * @return MockObject
     */
    private function getGovernmentMock(): MockObject
    {
        $mock = $this->createMock(Government::class);
        $mock->method('getData')->willReturn([]);
        return $mock;
    }
}
