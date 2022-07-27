<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Model;

use Magento\Framework\Filesystem\Io\File;

class FileChecker
{
    /**
     * @var File
     */
    private $file;

    /**
     * FileChecker constructor.
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param $destinationFile
     * @param int $sparseLevel
     * @return string
     */
    public function getNewFileName($destinationFile, $sparseLevel = 2)
    {
        $fileInfo = $this->file->getPathInfo($destinationFile);
        if ($this->file->fileExists($destinationFile)) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while ($this->file->fileExists($fileInfo['dirname'] . '/' . $baseName)) {
                $baseName = $fileInfo['filename'] . '_' . $index . '.' . $fileInfo['extension'];
                $index++;
            }
            return $baseName;
        } else {
            $prefix = $sparseLevel > 0 ? '/' : '';
            $fileName = $fileInfo['filename'];
            for ($i = 0; $i < $sparseLevel; $i++) {
                $prefix .= ($fileName[$i] ?? '_') . '/';
            }
            return $prefix . $fileInfo['basename'];
        }
    }
}
