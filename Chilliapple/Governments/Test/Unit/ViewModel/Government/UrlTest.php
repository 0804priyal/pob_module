<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\ViewModel\Government\Url;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @var UrlInterface | MockObject
     */
    private $urlBuilder;
    /**
     * @var GovernmentInterface | MockObject
     */
    private $government;
    /**
     * @var Url
     */
    private $url;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->government = $this->createMock(GovernmentInterface::class);
        $this->url = new Url($this->urlBuilder);
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::getListUrl
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::__construct
     */
    public function testGetListUrl()
    {
        $this->urlBuilder->expects($this->once())->method('getUrl')->willReturnArgument(0);
        $this->assertEquals('governments/government/index', $this->url->getListUrl());
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::getGovernmentUrl
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::getGovernmentUrlById
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::__construct
     */
    public function testGetGovernmentUrl()
    {
        $government = $this->createMock(GovernmentInterface::class);
        $government->method('getId')->willReturn(1);
        $this->urlBuilder->expects($this->once())->method('getUrl')
            ->with('governments/government/view', ['id' => 1])
            ->willReturn('url');
        $this->assertEquals('url', $this->url->getGovernmentUrl($government));
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::getGovernmentUrlById
     * @covers \Chilliapple\Governments\ViewModel\Government\Url::__construct
     */
    public function testGetGovernmentUrlById()
    {
        $this->urlBuilder->expects($this->once())->method('getUrl')
            ->with('governments/government/view', ['id' => 1])
            ->willReturn('url');
        $this->assertEquals('url', $this->url->getGovernmentUrlById(1));
    }
}
