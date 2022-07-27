<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml\Government;

use Magento\Framework\App\Action\HttpPostActionInterface;

class InlineEdit extends \Chilliapple\Governments\Controller\Adminhtml\InlineEdit implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Chilliapple_Governments::governments_government';
}
