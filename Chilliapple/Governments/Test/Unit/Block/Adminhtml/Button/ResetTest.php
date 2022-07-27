<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Block\Adminhtml\Button;

use Chilliapple\Governments\Block\Adminhtml\Button\Reset;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Reset::getButtonData
     */
    public function testGetButtonData()
    {
        $reset = new Reset();
        $result = $reset->getButtonData();
        $this->assertEquals(__('Reset'), $result['label']);
        $this->assertEquals("location.reload();", $result['on_click']);
    }
}
