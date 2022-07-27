<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Block\Adminhtml\Button;

use Chilliapple\Governments\Ui\EntityUiConfig;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var EntityUiConfig|null
     */
    private $uiConfig;

    /**
     * Back constructor.
     * @param UrlInterface $url
     * @param null|EntityUiConfig $uiConfig
     */
    public function __construct(UrlInterface $url, ?EntityUiConfig $uiConfig = null)
    {
        $this->url = $url;
        $this->uiConfig = $uiConfig;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => $this->getLabel(),
            'on_click' => sprintf("location.href = '%s';", $this->url->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * @return string
     */
    private function getLabel()
    {
        return $this->uiConfig ? $this->uiConfig->getBackLabel() : __('Back')->render();
    }
}
