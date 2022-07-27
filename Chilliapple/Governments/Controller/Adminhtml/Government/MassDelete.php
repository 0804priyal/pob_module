<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml\Government;

use Magento\Framework\App\Action\HttpPostActionInterface;

class MassDelete extends \Chilliapple\Governments\Controller\Adminhtml\MassDelete implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Chilliapple_Governments::governments_government';
}
