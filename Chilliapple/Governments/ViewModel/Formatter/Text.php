<?php

declare(strict_types=1);

namespace Chilliapple\Governments\ViewModel\Formatter;

use Magento\Framework\Escaper;

class Text implements FormatterInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Text constructor.
     * @param Escaper $escaper
     */
    public function __construct(Escaper $escaper)
    {
        $this->escaper = $escaper;
    }

    /**
     * @param $value
     * @param array $arguments
     * @return string
     */
    public function formatHtml($value, $arguments = []): string
    {
        return $this->escaper->escapeHtml($value);
    }
}
