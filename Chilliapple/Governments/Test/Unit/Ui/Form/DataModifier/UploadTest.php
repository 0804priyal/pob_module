<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui\Form\DataModifier;

use Chilliapple\Governments\Model\FileInfo;
use Chilliapple\Governments\Model\Uploader;
use Chilliapple\Governments\Ui\Form\DataModifier\Upload;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UploadTest extends TestCase
{
    /**
     * @var Uploader | MockObject
     */
    private $uploader;
    /**
     * @var LoggerInterface | MockObject
     */
    private $logger;
    /**
     * @var FileInfo | MockObject
     */
    private $fileInfo;
    /**
     * @var StoreManagerInterface | MockObject
     */
    private $storeManager;
    /**
     * @var AbstractModel | MockObject
     */
    private $model;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->uploader = $this->createMock(Uploader::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->fileInfo = $this->createMock(FileInfo::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->model = $this->createMock(AbstractModel::class);
    }

    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::__construct
     */
    public function testModifyDataNoValue()
    {
        $upload = new Upload(
            ['field1'],
            $this->uploader,
            $this->logger,
            $this->fileInfo,
            $this->storeManager
        );
        $data = ['dummy' => 'dummy', 'field1' => ''];
        $this->assertEquals($data, $upload->modifyData($this->model, $data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::__construct
     */
    public function testModifyDataNoMissingFile()
    {
        $upload = new Upload(
            ['field1'],
            $this->uploader,
            $this->logger,
            $this->fileInfo,
            $this->storeManager
        );
        $data = ['dummy' => 'dummy', 'field1' => 'file'];
        $this->fileInfo->method('isExist')->willReturn(false);
        $this->assertEquals($data, $upload->modifyData($this->model, $data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::__construct
     */
    public function testModifyData()
    {
        $upload = new Upload(
            ['field1'],
            $this->uploader,
            $this->logger,
            $this->fileInfo,
            $this->storeManager
        );
        $data = ['dummy' => 'dummy', 'field1' => 'file'];
        $this->fileInfo->method('isExist')->willReturn(true);
        $this->fileInfo->method('isBeginsWithMediaDirectoryPath')->willReturn(true);
        $this->fileInfo->method('getMimeType')->willReturn('mime');
        $this->fileInfo->method('getStat')->willReturn(['size' => 20]);
        $expected = [
            'dummy' => 'dummy',
            'field1' => [
                [
                    'name' => 'file',
                    'url' => 'file',
                    'size' => 20,
                    'type' => 'mime'
                ]
            ]
        ];
        $this->assertEquals($expected, $upload->modifyData($this->model, $data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::getUrl
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::__construct
     */
    public function testGetUrlNoFile()
    {
        $this->expectException(LocalizedException::class);
        $upload = new Upload(
            ['field1'],
            $this->uploader,
            $this->logger,
            $this->fileInfo,
            $this->storeManager
        );
        $upload->getUrl([]);
    }

    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::getUrl
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\Upload::__construct
     */
    public function testGetUrl()
    {
        $store = $this->createMock(Store::class);
        $store->method('getBaseUrl')->willReturn('base_url/');
        $this->storeManager->method('getStore')->willReturn($store);
        $this->fileInfo->method('getBaseFilePath')->willReturn('file_path');
        $upload = new Upload(
            ['field1'],
            $this->uploader,
            $this->logger,
            $this->fileInfo,
            $this->storeManager
        );
        $expected = 'base_url/file_path/file';
        $this->assertEquals($expected, $upload->getUrl('file'));
    }
}
