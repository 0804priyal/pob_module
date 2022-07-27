<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Ui\Form;

use Magento\Framework\Model\AbstractModel;

interface DataModifierInterface
{
    /**
     * @param AbstractModel $model
     * @param array $data
     * @return array
     */
    public function modifyData(AbstractModel $model, array $data): array;
}
