<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Block\Government;

use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Block\Government\Name;
use Chilliapple\Governments\ViewModel\Government\Url;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\File\Resolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @var GovernmentRepositoryInterface | MockObject
     */
    private $repository;
    /**
     * @var Url | MockObject
     */
    private $url;
    /**
     * @var StoreInterface | MockObject
     */
    private $store;
    /**
     * @var Name
     */
    private $name;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $context = $this->createMock(Context::class);
        $this->url = $this->createMock(Url::class);
        $this->repository = $this->createMock(GovernmentRepositoryInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $context->method('getStoreManager')->willReturn($storeManager);
        $this->store = $this->createMock(StoreInterface::class);
        $storeManager->method('getStore')->willReturn($this->store);
        $appState = $this->createMock(State::class);
        $context->method('getAppState')->willReturn($appState);
        $resolver = $this->createMock(Resolver::class);
        $context->method('getResolver')->willReturn($resolver);
        $urlBuilder = $this->createMock(UrlInterface::class);
        $context->method('getUrlBuilder')->willReturn($urlBuilder);
        $this->name = new Name(
            $context,
            $this->url,
            $this->repository
        );
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getLabel
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetLabel()
    {
        $this->assertEquals(null, $this->name->getLabel());
        $this->name->setData('label', 'label');
        $this->assertEquals('label', $this->name->getLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getCacheKeyInfo
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetCacheKeyInfo()
    {
        $this->name->setData('government_id', 1);
        $this->assertTrue(in_array(1, $this->name->getCacheKeyInfo()));
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernment()
    {
        $this->name->setData('government_id', 1);
        $government1 = $this->createMock(GovernmentInterface::class);
        $government1->method('getIsActive')->willReturn(1);
        $government1->method('getStoreId')->willReturn([0]);
        $government2 = $this->createMock(GovernmentInterface::class);
        $government2->method('getIsActive')->willReturn(1);
        $government2->method('getStoreId')->willReturn([1]);
        $this->store->method('getId')->willReturn(1);
        $this->repository->expects($this->exactly(2))->method('get')->willReturnOnConsecutiveCalls(
            $government1,
            $government2
        );
        $this->assertEquals($government1, $this->name->getGovernment());
        //call twice to test memoizing
        $this->assertEquals($government1, $this->name->getGovernment());
        $this->name->setData('government_id', 2);
        $this->assertEquals($government2, $this->name->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernmentInactive()
    {
        $this->name->setData('government_id', 1);
        $government = $this->createMock(GovernmentInterface::class);
        $government->method('getIsActive')->willReturn(0);
        $this->repository->expects($this->once())->method('get')->willReturn($government);
        $this->assertFalse($this->name->getGovernment());
        //call twice to test memoizing
        $this->assertFalse($this->name->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernmentNoValidStore()
    {
        $this->name->setData('government_id', 1);
        $government = $this->createMock(GovernmentInterface::class);
        $government->method('getIsActive')->willReturn(1);
        $this->store->method('getId')->willReturn(1);
        $government->method('getStoreId')->willReturn([2]);
        $this->repository->expects($this->once())->method('get')->willReturn($government);
        $this->assertFalse($this->name->getGovernment());
        //call twice to test memoizing
        $this->assertFalse($this->name->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernmentWithException()
    {
        $this->name->setData('government_id', 1);
        $this->repository->expects($this->once())->method('get')->willThrowException(
            $this->createMock(NoSuchEntityException::class)
        );
        $this->assertFalse($this->name->getGovernment());
        //call twice to test memoizing
        $this->assertFalse($this->name->getGovernment());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernmentUrl
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernmentUrlWithoutGovernment()
    {
        $this->name->setData('government_id', 1);
        $this->repository->expects($this->once())->method('get')->willThrowException(
            $this->createMock(NoSuchEntityException::class)
        );
        $this->url->expects($this->never())->method('getGovernmentUrl');
        $this->assertEquals('', $this->name->getGovernmentUrl());
    }

    /**
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernmentUrl
     * @covers \Chilliapple\Governments\Block\Government\Name::getGovernment
     * @covers \Chilliapple\Governments\Block\Government\Name::__construct
     */
    public function testGetGovernmentUrl()
    {
        $this->name->setData('government_id', 1);
        $government = $this->createMock(GovernmentInterface::class);
        $government->method('getIsActive')->willReturn(1);
        $government->method('getStoreId')->willReturn([0]);
        $this->store->method('getId')->willReturn(1);
        $this->repository->expects($this->once())->method('get')->willReturn($government);
        $this->url->expects($this->once())->method('getGovernmentUrl')->willReturn('url');
        $this->assertEquals('url', $this->name->getGovernmentUrl());
    }
}
