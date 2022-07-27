<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml\Government;

use Magento\Framework\App\Action\HttpPostActionInterface;

class Save extends \Chilliapple\Governments\Controller\Adminhtml\Save implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Chilliapple_Governments::governments_government';
}
