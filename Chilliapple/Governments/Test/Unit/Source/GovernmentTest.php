<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Source;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\Data\GovernmentSearchResultInterface;
use Chilliapple\Governments\Api\GovernmentListRepositoryInterface;
use Chilliapple\Governments\Source\Government;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GovernmentTest extends TestCase
{
    /**
     * @var GovernmentListRepositoryInterface | MockObject
     */
    private $repository;
    /**
     * @var Government | MockObject
     */
    private $government;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(GovernmentListRepositoryInterface::class);
        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $searchCriteriaBuilder->method('create')->willReturn($searchCriteria);
        $this->government = new Government($this->repository, $searchCriteriaBuilder);
    }

    /**
     * @covers \Chilliapple\Governments\Source\Government::toOptionArray
     * @covers \Chilliapple\Governments\Source\Government::__construct
     */
    public function testToOptionArray()
    {
        $searchResults = $this->createMock(GovernmentSearchResultInterface::class);
        $searchResults->expects($this->once())->method('getItems')->willReturn(
            [
                $this->getGovernmentMock('Government Two', 2),
                $this->getGovernmentMock('Government One', 1)
            ]
        );
        $this->repository->expects($this->once())->method('getList')->willReturn($searchResults);
        $expected = [
            [
                'label' => 'Government One',
                'value' => 1
            ],
            [
                'label' => 'Government Two',
                'value' => 2
            ],
        ];
        $this->assertEquals($expected, $this->government->toOptionArray());
        //call twice to test memoizing
        $this->assertEquals($expected, $this->government->toOptionArray());
    }

    /**
     * @param string $title
     * @param int $id
     * @return GovernmentInterface|MockObject
     */
    private function getGovernmentMock(string $title, int $id)
    {
        $mock = $this->createMock(GovernmentInterface::class);
        $mock->method('getTitle')->willReturn($title);
        $mock->method('getgovernmentId')->willReturn($id);
        return $mock;
    }
}
