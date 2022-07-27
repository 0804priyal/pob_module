<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Ui\SaveDataProcessor;

use Chilliapple\Governments\Ui\SaveDataProcessorInterface;

class NullProcessor implements SaveDataProcessorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }
}
