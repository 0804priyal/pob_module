<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml\Government\Image;

use Magento\Framework\App\Action\HttpPostActionInterface;

class Upload extends \Chilliapple\Governments\Controller\Adminhtml\Upload implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Chilliapple_Governments::governments_government';
}
