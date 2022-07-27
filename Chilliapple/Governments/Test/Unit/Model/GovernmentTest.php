<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Model\Government;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GovernmentTest extends TestCase
{
    /**
     * @var Context | MockObject
     */
    private $context;
    /**
     * @var Registry | MockObject
     */
    private $registry;
    /**
     * @var AbstractResource | MockObject
     */
    private $resource;
    /**
     * @var AbstractDb | MockObject
     */
    private $resourceCollection;
    /**
     * @var Government
     */
    private $government;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->registry = $this->createMock(Registry::class);
        $this->resource = $this->createMock(\Chilliapple\Governments\Model\ResourceModel\Government::class);
        $this->resourceCollection = $this->createMock(AbstractDb::class);
        $this->government = new Government(
            $this->context,
            $this->registry,
            $this->resource,
            $this->resourceCollection,
            []
        );
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::getGovernmentId
     * @covers \Chilliapple\Governments\Model\Government::setGovernmentId
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testGetGovernmentId()
    {
        $this->government->setGovernmentId(1);
        $this->assertEquals(1, $this->government->getGovernmentId());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setTitle
     * @covers \Chilliapple\Governments\Model\Government::getTitle
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetTitle()
    {
        $this->government->setTitle('title');
        $this->assertEquals('title', $this->government->getTitle());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setFeatureImage
     * @covers \Chilliapple\Governments\Model\Government::getFeatureImage
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetFeatureImage()
    {
        $this->government->setFeatureImage('feature_image');
        $this->assertEquals('feature_image', $this->government->getFeatureImage());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setDescription
     * @covers \Chilliapple\Governments\Model\Government::getDescription
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetDescription()
    {
        $this->government->setDescription('description');
        $this->assertEquals('description', $this->government->getDescription());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setGovernmentLink
     * @covers \Chilliapple\Governments\Model\Government::getGovernmentLink
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetGovernmentLink()
    {
        $this->government->setGovernmentLink('government_link');
        $this->assertEquals('government_link', $this->government->getGovernmentLink());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setStoreId
     * @covers \Chilliapple\Governments\Model\Government::getStoreId
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetStoreId()
    {
        $this->government->setStoreId([0, 1]);
        $this->assertEquals([0, 1], $this->government->getStoreId());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setMetaTitle
     * @covers \Chilliapple\Governments\Model\Government::getMetaTitle
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetMetaTitle()
    {
        $this->government->setMetaTitle('meta_title');
        $this->assertEquals('meta_title', $this->government->getMetaTitle());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::setMetaKeywords
     * @covers \Chilliapple\Governments\Model\Government::getMetaKeywords
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetMetaKeywords()
    {
        $this->government->setMetaKeywords('meta_keywords');
        $this->assertEquals('meta_keywords', $this->government->getMetaKeywords());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::getMetaDescription
     * @covers \Chilliapple\Governments\Model\Government::setMetaDescription
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetMetaDescription()
    {
        $this->government->setMetaDescription('meta_description');
        $this->assertEquals('meta_description', $this->government->getMetaDescription());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::getIsActive
     * @covers \Chilliapple\Governments\Model\Government::setIsActive
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testSetIsActive()
    {
        $this->government->setIsActive(1);
        $this->assertEquals(1, $this->government->getIsActive());
    }

    /**
     * @covers \Chilliapple\Governments\Model\Government::getIdentities
     * @covers \Chilliapple\Governments\Model\Government::_construct
     */
    public function testGetIdentities()
    {
        $this->assertEquals(['chilliapple_governments_government_'], $this->government->getIdentities());
        $this->government->setId(1);
        $this->assertEquals(['chilliapple_governments_government_1'], $this->government->getIdentities());
    }
}
