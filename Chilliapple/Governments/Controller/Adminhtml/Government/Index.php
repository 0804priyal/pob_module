<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml\Government;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends \Chilliapple\Governments\Controller\Adminhtml\Index implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Chilliapple_Governments::governments_government';
}
