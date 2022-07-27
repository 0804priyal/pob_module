<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Api\Data\GovernmentInterfaceFactory;
use Chilliapple\Governments\Model\GovernmentRepo;
use Chilliapple\Governments\Model\ResourceModel\Government;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GovernmentRepoTest extends TestCase
{
    /**
     * @var GovernmentInterfaceFactory | MockObject
     */
    private $factory;
    /**
     * @var Government | MockObject
     */
    private $resource;
    /**
     * @var \Chilliapple\Governments\Model\Government | MockObject
     */
    private $government;
    /**
     * @var GovernmentRepo
     */
    private $governmentRepo;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->factory = $this->createMock(GovernmentInterfaceFactory::class);
        $this->resource = $this->createMock(Government::class);
        $this->government = $this->createMock(\Chilliapple\Governments\Model\Government::class);
        $this->governmentRepo = new GovernmentRepo(
            $this->factory,
            $this->resource
        );
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::save
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testSave()
    {
        $this->resource->expects($this->once())->method('save');
        $this->assertEquals($this->government, $this->governmentRepo->save($this->government));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::save
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testSaveWithSaveError()
    {
        $this->expectException(CouldNotSaveException::class);
        $this->resource->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->governmentRepo->save($this->government);
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::get
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testGet()
    {
        $this->resource->expects($this->once())->method('load');
        $this->factory->method('create')->willReturn($this->government);
        $this->government->method('getId')->willReturn(1);
        $this->assertEquals($this->government, $this->governmentRepo->get(1));
        //call twice to test memoizing
        $this->assertEquals($this->government, $this->governmentRepo->get(1));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::get
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testGetWithMissingId()
    {
        $this->resource->expects($this->once())->method('load');
        $this->factory->method('create')->willReturn($this->government);
        $this->government->method('getId')->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->governmentRepo->get(1);
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::delete
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testDelete()
    {
        $this->resource->expects($this->once())->method('delete');
        $this->assertTrue($this->governmentRepo->delete($this->government));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::delete
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testDeleteWithError()
    {
        $this->resource->expects($this->once())->method('delete')->willThrowException(new \Exception());
        $this->expectException(CouldNotDeleteException::class);
        $this->governmentRepo->delete($this->government);
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::deleteById
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testDeleteById()
    {
        $this->resource->expects($this->once())->method('load');
        $this->factory->method('create')->willReturn($this->government);
        $this->government->method('getId')->willReturn(1);
        $this->resource->expects($this->once())->method('delete');
        $this->assertTrue($this->governmentRepo->deleteById(1));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::clear
     * @covers \Chilliapple\Governments\Model\GovernmentRepo::__construct
     */
    public function testClear()
    {
        $this->assertEquals([], $this->governmentRepo->clear());
    }
}
