<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui\SaveDataProcessor;

use Chilliapple\Governments\Ui\SaveDataProcessorInterface;
use Chilliapple\Governments\Ui\SaveDataProcessor\CompositeProcessor;
use PHPUnit\Framework\TestCase;

class CompositeProcessorTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\CompositeProcessor::modifyData
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\CompositeProcessor::__construct
     */
    public function testModifyData()
    {
        $processor1 = $this->createMock(SaveDataProcessorInterface::class);
        $processor1->method('modifyData')->willReturnCallback(
            function (array $data) {
                $data['element1'] = ($data['element1'] ?? '') . '_processed1';
                return $data;
            }
        );
        $processor2 = $this->createMock(SaveDataProcessorInterface::class);
        $processor2->method('modifyData')->willReturnCallback(
            function (array $data) {
                $data['element1'] = ($data['element1'] ?? '') . '_processed2';
                $data['element2'] = ($data['element2'] ?? '') . '_processed2';
                return $data;
            }
        );
        $compositeProcessor = new CompositeProcessor([$processor1, $processor2]);
        $data = [
            'element1' => 'value1',
            'element2' => 'value2',
            'element3' => 'value3'
        ];
        $expected = [
            'element1' => 'value1_processed1_processed2',
            'element2' => 'value2_processed2',
            'element3' => 'value3'
        ];
        $this->assertEquals($expected, $compositeProcessor->modifyData($data));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\SaveDataProcessor\CompositeProcessor::__construct
     */
    public function testGetConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CompositeProcessor(['string value']);
    }
}
