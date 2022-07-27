<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui\Form\DataModifier;

use Chilliapple\Governments\Ui\Form\DataModifier\NullModifier;
use Magento\Framework\Model\AbstractModel;
use PHPUnit\Framework\TestCase;

class NullModifierTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Ui\Form\DataModifier\NullModifier::modifyData
     */
    public function testModifyData()
    {
        $model = $this->createMock(AbstractModel::class);
        $data = ['dummy'];
        $this->assertEquals($data, (new NullModifier())->modifyData($model, $data));
    }
}
