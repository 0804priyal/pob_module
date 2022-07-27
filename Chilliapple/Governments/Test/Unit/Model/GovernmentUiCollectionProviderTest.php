<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Model\GovernmentUiCollectionProvider;
use Chilliapple\Governments\Model\ResourceModel\Government\Collection;
use Chilliapple\Governments\Model\ResourceModel\Government\CollectionFactory;
use PHPUnit\Framework\TestCase;

class GovernmentUiCollectionProviderTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Model\GovernmentUiCollectionProvider
     */
    public function testGetCollection()
    {
        $factory = $this->createMock(CollectionFactory::class);
        $collection = $this->createMock(Collection::class);
        $factory->expects($this->once())->method('create')->willReturn($collection);
        $provider = new governmentUiCollectionProvider($factory);
        $this->assertEquals($collection, $provider->getCollection());
    }
}
