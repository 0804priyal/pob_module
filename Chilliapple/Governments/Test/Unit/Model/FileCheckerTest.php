<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Model;

use Chilliapple\Governments\Model\FileChecker;
use Magento\Framework\Filesystem\Io\File;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileCheckerTest extends TestCase
{
    /**
     * @var File | MockObject
     */
    private $file;
    /**
     * @var FileChecker
     */
    private $fileChecker;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->file = $this->createMock(File::class);
        $this->fileChecker = new FileChecker($this->file);
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileChecker::getNewFileName
     * @covers \Chilliapple\Governments\Model\FileChecker::__construct
     */
    public function testGetNewFileName()
    {
        $this->file->method('getPathInfo')->willReturn([
            'filename' => 'file',
            'extension' => 'ext',
            'basename' => 'file.ext',
            'dirname' => 'dir'
        ]);
        $this->file->method('fileExists')->willReturn(false);
        $this->assertEquals('file.ext', $this->fileChecker->getNewFileName('file', 0));
        $this->assertEquals('/f/i/file.ext', $this->fileChecker->getNewFileName('file'));
        $this->assertEquals('/f/i/l/e/_/_/file.ext', $this->fileChecker->getNewFileName('file', 6));
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileChecker::getNewFileName
     * @covers \Chilliapple\Governments\Model\FileChecker::__construct
     */
    public function testGetNewFileNameFileExists()
    {
        $this->file->method('getPathInfo')->willReturn([
            'filename' => 'file',
            'extension' => 'ext',
            'basename' => 'file.ext',
            'dirname' => 'dir'
        ]);
        $this->file->method('fileExists')->willReturnOnConsecutiveCalls(true, true, false);
        $this->assertEquals('file_1.ext', $this->fileChecker->getNewFileName('file', 0));
    }

    /**
     * @covers \Chilliapple\Governments\Model\FileChecker::getNewFileName
     * @covers \Chilliapple\Governments\Model\FileChecker::__construct
     */
    public function testGetNewFileNameFileExistsThreeLevels()
    {
        $this->file->method('getPathInfo')->willReturn([
            'filename' => 'file',
            'extension' => 'ext',
            'basename' => 'file.ext',
            'dirname' => 'dir'
        ]);
        $this->file->method('fileExists')->willReturnOnConsecutiveCalls(true, true, true, true, false);
        $this->assertEquals('file_3.ext', $this->fileChecker->getNewFileName('file', 0));
    }
}
