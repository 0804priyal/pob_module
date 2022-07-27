<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Block\Adminhtml\Button;

use Chilliapple\Governments\Block\Adminhtml\Button\Save;
use Chilliapple\Governments\Ui\EntityUiConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SaveTest extends TestCase
{
    /**
     * @var EntityUiConfig | MockObject
     */
    private $uiConfig;
    /**
     * @var Save
     */
    private $save;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->uiConfig = $this->createMock(EntityUiConfig::class);
        $this->save = new Save($this->uiConfig);
    }

    /**
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getButtonData
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::hasOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::__construct
     */
    public function testGetButtonData()
    {
        $this->uiConfig->method('getSaveFormTarget')->willReturn('target');
        $this->uiConfig->method('getAllowSaveAndClose')->willReturn(true);
        $this->uiConfig->method('getAllowSaveAndDuplicate')->willReturn(true);
        $this->uiConfig->expects($this->once())->method('getSaveAndCloseLabel')->willReturn('save and close');
        $this->uiConfig->expects($this->once())->method('getSaveLabel')->willReturn('save entity');
        $this->uiConfig->expects($this->once())->method('getSaveAndDuplicateLabel')->willReturn('save and duplicate');
        $result = $this->save->getButtonData();
        $this->assertEquals('save entity', $result['label']);
        $this->assertEquals(
            'target',
            $result['data_attribute']['mage-init']['buttonAdapter']['actions'][0]['targetName']
        );
        $this->assertEquals(2, count($result['options']));
    }

    /**
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getButtonData
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::hasOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::__construct
     */
    public function testGetButtonDataNoClose()
    {
        $this->uiConfig->method('getSaveFormTarget')->willReturn('target');
        $this->uiConfig->method('getAllowSaveAndClose')->willReturn(false);
        $this->uiConfig->method('getAllowSaveAndDuplicate')->willReturn(true);
        $this->uiConfig->expects($this->never())->method('getSaveAndCloseLabel');
        $this->uiConfig->expects($this->once())->method('getSaveLabel')->willReturn('save entity');
        $this->uiConfig->expects($this->once())->method('getSaveAndDuplicateLabel')->willReturn('save and duplicate');
        $result = $this->save->getButtonData();
        $this->assertEquals('save entity', $result['label']);
        $this->assertEquals(
            'target',
            $result['data_attribute']['mage-init']['buttonAdapter']['actions'][0]['targetName']
        );
        $this->assertEquals(1, count($result['options']));
    }

    /**
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getButtonData
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::hasOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::getOptions
     * @covers \Chilliapple\Governments\Block\Adminhtml\Button\Save::__construct
     */
    public function testGetButtonDataNoOptions()
    {
        $this->uiConfig->method('getSaveFormTarget')->willReturn('target');
        $this->uiConfig->method('getAllowSaveAndClose')->willReturn(false);
        $this->uiConfig->method('getAllowSaveAndDuplicate')->willReturn(false);
        $this->uiConfig->expects($this->never())->method('getSaveAndCloseLabel');
        $this->uiConfig->expects($this->once())->method('getSaveLabel')->willReturn('save entity');
        $this->uiConfig->expects($this->never())->method('getSaveAndDuplicateLabel');
        $result = $this->save->getButtonData();
        $this->assertEquals('save entity', $result['label']);
        $this->assertEquals(
            'target',
            $result['data_attribute']['mage-init']['buttonAdapter']['actions'][0]['targetName']
        );
        $this->assertEquals([], $result['options']);
    }
}
