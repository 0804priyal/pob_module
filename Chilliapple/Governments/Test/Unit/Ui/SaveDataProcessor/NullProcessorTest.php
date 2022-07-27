<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui\SaveDataProcessor;

use Chilliapple\Governments\Ui\SaveDataProcessor\NullProcessor;
use PHPUnit\Framework\TestCase;

class NullProcessorTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\NullProcessor
     */
    public function testModifyData()
    {
        $data = ['dummy'];
        $this->assertEquals($data, (new NullProcessor())->modifyData($data));
    }
}
