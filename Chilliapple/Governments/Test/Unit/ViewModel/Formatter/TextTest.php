<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\ViewModel\Formatter;

use Chilliapple\Governments\ViewModel\Formatter\Text;
use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @var Escaper | MockObject
     */
    private $escaper;
    /**
     * @var Text
     */
    private $text;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->escaper = $this->createMock(Escaper::class);
        $this->text = new Text($this->escaper);
    }

    /**
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Text::formatHtml
     * @covers \Chilliapple\Governments\ViewModel\Formatter\Text::__construct
     */
    public function testFormatHtml()
    {
        $this->escaper->expects($this->once())->method('escapeHtml')->willReturn('escaped');
        $this->assertEquals('escaped', $this->text->formatHtml('value'));
    }
}
