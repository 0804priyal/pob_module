<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Ui\Form\DataModifier;

use Chilliapple\Governments\Ui\Form\DataModifierInterface;
use Magento\Framework\Model\AbstractModel;

class CompositeDataModifier implements DataModifierInterface
{
    /**
     * @var DataModifierInterface[]
     */
    private $modifiers;

    /**
     * CompositeDataModifier constructor.
     * @param DataModifierInterface[] $modifiers
     */
    public function __construct(array $modifiers)
    {
        foreach ($modifiers as $modifier) {
            if (!($modifier instanceof DataModifierInterface)) {
                throw new \InvalidArgumentException(
                    "Form data modifier must implemenet " . DataModifierInterface::class
                );
            }
        }
        $this->modifiers = $modifiers;
    }

    /**
     * @param AbstractModel $model
     * @param array $data
     * @return array
     */
    public function modifyData(AbstractModel $model, array $data): array
    {
        foreach ($this->modifiers as $modifier) {
            $data = $modifier->modifyData($model, $data);
        }
        return $data;
    }
}
