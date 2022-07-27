<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\ViewModel\Government\View;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var RequestInterface | MockObject
     */
    private $request;
    /**
     * @var GovernmentRepositoryInterface | MockObject
     */
    private $governmentRepository;
    /**
     * @var View
     */
    private $view;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
        $this->governmentRepository = $this->createMock(GovernmentRepositoryInterface::class);
        $this->view = new View(
            $this->request,
            $this->governmentRepository
        );
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\View::getGovernment
     * @covers \Chilliapple\Governments\ViewModel\Government\View::__construct
     */
    public function testGetGovernment()
    {
        $this->request->expects($this->once())->method('getParam')->willReturn(1);
        $government = $this->createMock(GovernmentInterface::class);
        $this->governmentRepository->expects($this->once())->method('get')->willReturn($government);
        $this->assertEquals($government, $this->view->getGovernment());
        //call twice to test memoizing
        $this->assertEquals($government, $this->view->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\View::getGovernment
     * @covers \Chilliapple\Governments\ViewModel\Government\View::__construct
     */
    public function testGetGovernmentWithException()
    {
        $this->request->expects($this->once())->method('getParam')->willReturn(1);
        $this->governmentRepository->expects($this->once())->method('get')->willThrowException(
            $this->createMock(NoSuchEntityException::class)
        );
        $this->assertFalse($this->view->getGovernment());
        //call twice to test memoizing
        $this->assertFalse($this->view->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\View::getGovernment
     * @covers \Chilliapple\Governments\ViewModel\Government\View::__construct
     */
    public function testGetGovernmentNoId()
    {
        $this->request->expects($this->once())->method('getParam')->willReturn(null);
        $this->governmentRepository->expects($this->never())->method('get');
        $this->assertFalse($this->view->getGovernment());
        //call twice to test memoizing
        $this->assertFalse($this->view->getGovernment());
    }
}
