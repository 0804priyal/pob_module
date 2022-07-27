<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Controller\Adminhtml;

use Chilliapple\Governments\Controller\Adminhtml\NewAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use PHPUnit\Framework\TestCase;

class NewActionTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Controller\Adminhtml\NewAction::execute
     */
    public function testExecute()
    {
        $context = $this->createMock(Context::class);
        $resultFactory = $this->createMock(ResultFactory::class);
        $forward = $this->createMock(Forward::class);
        $context->expects($this->once())->method('getResultFactory')->willReturn($resultFactory);
        $resultFactory->expects($this->once())->method('create')->willReturn($forward);
        $newAction = new NewAction($context);
        $forward->expects($this->once())->method('forward')->with('edit');
        $this->assertEquals($forward, $newAction->execute());
    }
}
