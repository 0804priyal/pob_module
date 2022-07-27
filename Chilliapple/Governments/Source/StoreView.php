<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options;

class StoreView extends Options implements OptionSourceInterface
{
    /**
     * All Store Views value
     */
    public const ALL_STORE_VIEWS = '0';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
