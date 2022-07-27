<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel\Formatter;

use Chilliapple\Governments\Model\FileInfo;
use Chilliapple\Governments\Model\FileInfoFactory;
use Chilliapple\Governments\ViewModel\Formatter\Image;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Image\Adapter\AbstractAdapter;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * @var AdapterFactory | MockObject
     */
    private $adapterFactory;
    /**
     * @var FileInfoFactory | MockObject
     */
    private $fileInfoFactory;
    /**
     * @var Filesystem | MockObject
     */
    private $filesystem;
    /**
     * @var StoreManagerInterface | MockObject
     */
    private $storeManager;
    /**
     * @var Image
     */
    private $image;
    /**
     * @var Filesystem\Directory\WriteInterface | MockObject
     */
    private $mediaDir;
    /**
     * @var FileInfo | MockObject
     */
    private $fileInfo;
    /**
     * @var Store | MockObject
     */
    private $store;
    /**
     * @var AbstractAdapter | MockObject
     */
    private $adapter;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->adapterFactory = $this->createMock(AdapterFactory::class);
        $this->fileInfoFactory = $this->createMock(FileInfoFactory::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->mediaDir = $this->createMock(Filesystem\Directory\WriteInterface::class);
        $this->fileInfo = $this->createMock(FileInfo::class);
        $this->store = $this->createMock(Store::class);
        $this->adapter = $this->createMock(AbstractAdapter::class);
        $this->image = new Image(
            $this->adapterFactory,
            $this->fileInfoFactory,
            $this->filesystem,
            $this->storeManager
        );
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getFileInfo
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::__construct
     */
    public function testFormatHtmlNoPath()
    {
        $this->fileInfoFactory->method('create')->willReturn($this->fileInfo);
        $this->fileInfo->method('getAbsoluteFilePath')->willReturn('');
        $this->filesystem->expects($this->never())->method('getDirectoryWrite');
        $this->assertEquals('', $this->image->formatHtml('value'));
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getFileInfo
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getDestinationRelativePath
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getImageSettings
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::__construct
     */
    public function testFormatHtml()
    {
        $this->fileInfoFactory->method('create')->willReturn($this->fileInfo);
        $this->fileInfo->method('getFilePath')->willReturn('absolute/path/to/image/path/goes/here.jpg');
        $this->fileInfo->method('getAbsoluteFilePath')->willReturn('absolute/path');
        $this->filesystem->method('getDirectoryWrite')->willReturn($this->mediaDir);
        $this->mediaDir->method('isFile')->willReturn(false);
        $this->adapterFactory->method('create')->willReturn($this->adapter);
        $this->storeManager->method('getStore')->willReturn($this->store);
        $this->store->method('getBaseUrl')->willReturn('base/');
        $this->assertEquals(
            'base/absolute/path/to/image/cache/832a442eaed87e27dfb80e2a897dd624/path/goes/here.jpg',
            $this->image->formatHtml('image/path/goes/here.jpg', ['resize' => [100, 100]])
        );
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getFileInfo
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getDestinationRelativePath
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::getImageSettings
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Image::__construct
     */
    public function testFormatHtmlWithStringResize()
    {
        $this->fileInfoFactory->method('create')->willReturn($this->fileInfo);
        $this->fileInfo->method('getFilePath')->willReturn('absolute/path/to/image/path/goes/here.jpg');
        $this->fileInfo->method('getAbsoluteFilePath')->willReturn('absolute/path');
        $this->filesystem->method('getDirectoryWrite')->willReturn($this->mediaDir);
        $this->mediaDir->method('isFile')->willReturn(false);
        $this->adapterFactory->method('create')->willReturn($this->adapter);
        $this->storeManager->method('getStore')->willReturn($this->store);
        $this->store->method('getBaseUrl')->willReturn('base/');
        $this->assertEquals(
            'base/absolute/path/to/image/path/cache/ed589c087cddf99587dd985c5d36d463/goes/here.jpg',
            $this->image->formatHtml('image/path/goes/here.jpg', ['resize' => 100, 'image_name_parts' => 2])
        );
    }
}
