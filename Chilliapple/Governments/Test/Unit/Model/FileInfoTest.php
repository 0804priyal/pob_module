<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Model\FileInfo;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    /**
     * @var Filesystem | MockObject
     */
    private $filesystem;
    /**
     * @var Mime | MockObject
     */
    private $mime;
    /**
     * @var StoreManagerInterface | MockObject
     */
    private $storeManager;
    /**
     * @var Store | MockObject
     */
    private $store;
    /**
     * @var FileInfo
     */
    private $fileInfo;
    /**
     * @var Filesystem\Directory\WriteInterface | MockObject
     */
    private $mediaDirectory;
    /**
     * @var Filesystem\Directory\ReadInterface | MockObject
     */
    private $readDirectory;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->mime = $this->createMock(Mime::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->store = $this->createMock(Store::class);
        $this->storeManager->method('getStore')->willReturn($this->store);
        $this->store->method('getBaseUrl')->willReturn('base_url/');
        $this->mediaDirectory = $this->createMock(Filesystem\Directory\WriteInterface::class);
        $this->readDirectory = $this->createMock(Filesystem\Directory\ReadInterface::class);
        $this->filesystem->method('getDirectoryWrite')->willReturn($this->mediaDirectory);
        $this->filesystem->method('getDirectoryRead')->willReturn($this->readDirectory);
        $this->fileInfo = new FileInfo(
            $this->filesystem,
            $this->mime,
            $this->storeManager,
            'base/path'
        );
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileInfo::getBaseFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::__construct
     */
    public function testGetBaseFilePath()
    {
        $this->assertEquals('/base/path', $this->fileInfo->getBaseFilePath());
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileInfo::getMimeType
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::getPubDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getBaseDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectoryPathRelativeToBaseDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::removeStorePath
     * @covers \Chilliapple\Governments\Model\FileInfo::isBeginsWithMediaDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::__construct
     */
    public function testGetMimeType()
    {
        $this->readDirectory->method('getAbsolutePath')->willReturn('absolute/path');
        $this->readDirectory->method('getRelativePath')->willReturn('relative');
        $this->mediaDirectory->method('getAbsolutePath')->willReturn('media/absolute/path');
        $this->mime->method('getMimeType')->willReturn('mime');
        $this->assertEquals('mime', $this->fileInfo->getMimeType('some/file.png'));
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileInfo::getStat
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::getPubDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getBaseDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectoryPathRelativeToBaseDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::removeStorePath
     * @covers \Chilliapple\Governments\Model\FileInfo::isBeginsWithMediaDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::__construct
     */
    public function testGetStat()
    {
        $this->readDirectory->method('getAbsolutePath')->willReturn('absolute/path');
        $this->readDirectory->method('getRelativePath')->willReturn('relative');
        $this->mediaDirectory->method('getAbsolutePath')->willReturn('media/absolute/path');
        $this->mediaDirectory->method('stat')->willReturn(['stat']);
        $this->assertEquals(['stat'], $this->fileInfo->getStat('some/file.png'));
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileInfo::isExist
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::getPubDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getBaseDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectoryPathRelativeToBaseDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::removeStorePath
     * @covers \Chilliapple\Governments\Model\FileInfo::isBeginsWithMediaDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::__construct
     */
    public function testIsExist()
    {
        $this->readDirectory->method('getAbsolutePath')->willReturn('absolute/path');
        $this->readDirectory->method('getRelativePath')->willReturn('relative');
        $this->mediaDirectory->method('getAbsolutePath')->willReturn('media/absolute/path');
        $this->mediaDirectory->method('isExist')->willReturn(true);
        $this->assertTrue($this->fileInfo->isExist('some/file.png'));
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileInfo::getAbsoluteFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getFilePath
     * @covers \Chilliapple\Governments\Model\FileInfo::getPubDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getBaseDirectory
     * @covers \Chilliapple\Governments\Model\FileInfo::getMediaDirectoryPathRelativeToBaseDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::removeStorePath
     * @covers \Chilliapple\Governments\Model\FileInfo::isBeginsWithMediaDirectoryPath
     * @covers \Chilliapple\Governments\Model\FileInfo::__construct
     */
    public function testGetAbsoluteFilePath()
    {
        $this->readDirectory->method('getAbsolutePath')->willReturn('absolute/path');
        $this->readDirectory->method('getRelativePath')->willReturn('relative');
        $this->mediaDirectory->method('getAbsolutePath')->willReturn('media/absolute/path');
        $this->mediaDirectory->method('isExist')->willReturn(true);
        $this->assertEquals('media/absolute/path', $this->fileInfo->getAbsoluteFilePath('some/file.png'));
    }
}
