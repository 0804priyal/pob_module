<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui\SaveDataProcessor;

use Chilliapple\Governments\Model\FileInfo;
use Chilliapple\Governments\Model\Uploader;
use Chilliapple\Governments\Ui\SaveDataProcessor\Upload;
use Magento\Framework\Filesystem;
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
     * @var FileInfo | MockObject
     */
    private $fileInfo;
    /**
     * @var Filesystem | MockObject
     */
    private $filesystem;
    /**
     * @var LoggerInterface | MockObject
     */
    private $logger;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->uploader = $this->createMock(Uploader::class);
        $this->fileInfo = $this->createMock(FileInfo::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::fileResidesOutsideUploadDir
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::getUploadedName
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::isTmpFileAvailable
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::__construct
     */
    public function testModifyData()
    {
        $this->fileInfo->method('getFilePath')->willReturnArgument(0);
        $upload = new Upload(
            ['field1', 'field2', 'field3', 'field4'],
            $this->uploader,
            $this->fileInfo,
            $this->filesystem,
            $this->logger,
            false
        );
        $uploadStrict = new Upload(
            ['field1', 'field2', 'field3', 'field4'],
            $this->uploader,
            $this->fileInfo,
            $this->filesystem,
            $this->logger,
            true
        );
        $data = [
            'field1' => [
                [
                    'tmp_name' => 'tmp_name',
                    'file' => 'file1',
                    'url' => 'path/url',
                    'name' => 'path/url'
                ]
            ],
            'field2' => [
                [
                    'url' => 'value2',
                    'name' => 'value2'
                ]
            ],
            'field3' => [],
            'dummy' => 'dummy'
        ];
        $this->filesystem->method('getUri')->willReturn('path');
        $this->uploader->method('moveFileFromTmp')->willReturn('tmp_moved');

        $expected = [
            'field1' => 'tmp_moved',
            'field2' => 'value2',
            'field3' => '',
            'dummy' => 'dummy'
        ];
        $expectedStrict = [
            'field1' => 'tmp_moved',
            'field2' => 'value2',
            'field3' => '',
            'dummy' => 'dummy',
            'field4' => ''
        ];
        $this->assertEquals($expected, $upload->modifyData($data));
        $this->assertEquals($expectedStrict, $uploadStrict->modifyData($data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::fileResidesOutsideUploadDir
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::getUploadedName
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::isTmpFileAvailable
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::__construct
     */
    public function testModifyDataWithException()
    {
        $this->fileInfo->method('getFilePath')->willReturnArgument(0);
        $upload = new Upload(
            ['field1', 'field2', 'field3', 'field4'],
            $this->uploader,
            $this->fileInfo,
            $this->filesystem,
            $this->logger,
            false
        );
        $data = [
            'field1' => [
                [
                    'tmp_name' => 'tmp_name',
                    'file' => 'file1',
                    'url' => 'path/url',
                    'name' => 'path/url',
                ]
            ],
            'field2' => [
                [
                'url' => 'value2',
                'name' => 'value2'
                ]
            ],
            'field3' => [],
            'dummy' => 'dummy'
        ];
        $this->filesystem->method('getUri')->willReturn('path');
        $this->uploader->expects($this->once())->method('moveFileFromTmp')->willThrowException(new \Exception());
        $this->logger->expects($this->once())->method('critical');
        $expected = [
            'field1' => [
                0 => [
                    'tmp_name' => 'tmp_name',
                    'file' => 'file1',
                    'url' => 'path/url',
                    'name' => 'path/url',
                    ],
                ],
            'field2' => 'value2',
            'field3' => '',
            'dummy' => 'dummy'
        ];
        $this->assertEquals($expected, $upload->modifyData($data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::modifyData
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::fileResidesOutsideUploadDir
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::getUploadedName
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::isTmpFileAvailable
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\Upload::__construct
     */
    public function testModifyDataFileOutside()
    {
        $upload = new Upload(
            ['field1', 'field2', 'field3', 'field4'],
            $this->uploader,
            $this->fileInfo,
            $this->filesystem,
            $this->logger,
            false
        );
        $this->fileInfo->method('getFilePath')->willReturn('media/path');
        $this->filesystem->method('getUri')->willReturn('media');
        $data = [
            'field1' => [
                [
                    'file' => 'file1',
                    'name' => 'media/path/url',
                    'url' => 'media/path/url'
                ]
            ],
        ];
        $expected = [
            'field1' => 'media/path/url'
        ];
        $this->assertEquals($expected, $upload->modifyData($data));
    }
}
