<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel;

use Chilliapple\Governments\ViewModel\Formatter;
use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    /**
     * @var Escaper | MockObject
     */
    private $escaper;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->escaper = $this->createMock(Escaper::class);
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter::getFormatter
     * @covers \Chilliapple\Governments\ViewModel\Formatter::__construct
     */
    public function testFormatHtml()
    {
        $formatter1 = $this->getFormatterMock();
        $formatter2 = $this->getFormatterMock();
        $formatter = new Formatter(
            [
                'type1' => $formatter1,
                'type2' => $formatter2,
            ],
            $this->escaper
        );
        $formatter1->expects($this->once())->method('formatHtml')->willReturn('formatted');
        $this->assertEquals('formatted', $formatter->formatHtml('value', ['type' => 'type1']));
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter::getFormatter
     * @covers \Chilliapple\Governments\ViewModel\Formatter::__construct
     */
    public function testFormatHtmlNoArgument()
    {
        $formatter1 = $this->getFormatterMock();
        $formatter2 = $this->getFormatterMock();
        $formatter = new Formatter(
            [
                'type1' => $formatter1,
                'type2' => $formatter2,
            ],
            $this->escaper
        );
        $formatter1->expects($this->never())->method('formatHtml');
        $this->escaper->expects($this->once())->method('escapeHtml')->willReturn('formatted');
        $this->assertEquals('formatted', $formatter->formatHtml('value', []));
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter::getFormatter
     * @covers \Chilliapple\Governments\ViewModel\Formatter::__construct
     */
    public function testFormatHtmlNoTypeConfigured()
    {
        $formatter1 = $this->getFormatterMock();
        $formatter2 = $this->getFormatterMock();
        $formatter = new Formatter(
            [
                'type1' => $formatter1,
                'type2' => $formatter2,
            ],
            $this->escaper
        );
        $this->expectException(\InvalidArgumentException::class);
        $formatter->formatHtml('value', ['type' => 'type3']);
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter::__construct
     */
    public function testConstruct()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Formatter(
            [
                'type1' => $this->getFormatterMock(),
                'type2' => 'string',
            ],
            $this->escaper
        );
    }

    /**
     * @return Formatter'FormatterInterface | MockObject
     * @throws \ReflectionException
     */
    private function getFormatterMock()
    {
        $formatter = $this->createMock(Formatter\FormatterInterface::class);
        return $formatter;
    }
}
