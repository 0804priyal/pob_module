<?php

declare(strict_types=1);

namespace Chilliapple\Governments\ViewModel\Formatter;

class Wysiwyg implements FormatterInterface
{
    /**
     * @var \Zend_Filter_Interface
     */
    private $filter;

    /**
     * Wysiwyg constructor.
     * @param \Zend_Filter_Interface $filter
     */
    public function __construct(\Zend_Filter_Interface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param $value
     * @param array $arguments
     * @return string
     * @throws \Zend_Filter_Exception
     */
    public function formatHtml($value, $arguments = []): string
    {
        return $this->filter->filter($value);
    }
}
