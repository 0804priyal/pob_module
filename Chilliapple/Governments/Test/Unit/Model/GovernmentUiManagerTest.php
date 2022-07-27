<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Api\governmentRepositoryInterface;
use Chilliapple\Governments\Model\Government;
use Chilliapple\Governments\Model\GovernmentFactory;
use Chilliapple\Governments\Model\GovernmentUiManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GovernmentUiManagerTest extends TestCase
{
    /**
     * @var GovernmentRepositoryInterface | MockObject
     */
    private $repository;
    /**
     * @var GovernmentFactory | MockObject
     */
    private $factory;
    /**
     * @var Government | MockObject
     */
    private $government;
    /**
     * @var GovernmentUiManager
     */
    private $governmentUiManager;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(GovernmentRepositoryInterface::class);
        $this->factory = $this->createMock(GovernmentFactory::class);
        $this->government = $this->createMock(Government::class);
        $this->governmentUiManager = new GovernmentUiManager(
            $this->repository,
            $this->factory
        );
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::get
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::__construct
     */
    public function testGetWithId()
    {
        $this->repository->expects($this->once())->method('get')->with(1)->willReturn($this->government);
        $this->factory->expects($this->never())->method('create');
        $this->assertEquals($this->government, $this->governmentUiManager->get(1));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::get
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::__construct
     */
    public function testGetWithoutId()
    {
        $this->repository->expects($this->never())->method('get');
        $this->factory->expects($this->once())->method('create')->willReturn($this->government);
        $this->assertEquals($this->government, $this->governmentUiManager->get(null));
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::save
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::__construct
     */
    public function testSave()
    {
        $this->repository->expects($this->once())->method('save');
        $this->governmentUiManager->save($this->government);
    }

    /**
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::delete
     * @covers \Chilliapple\Governments\Model\GovernmentUiManager::__construct
     */
    public function testDelete()
    {
        $this->repository->expects($this->once())->method('deleteById')->with(1);
        $this->governmentUiManager->delete(1);
    }
}
