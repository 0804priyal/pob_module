<?php
namespace Chilliapple\Redirect\Block\Adminhtml\Button\Redirect;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Delete constructor.
     * @param Registry $registry
     * @param UrlInterface $url
     */
    public function __construct(Registry $registry, UrlInterface $url)
    {
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getRedirectId()) {
            $data = [
                'label' => __('Delete Redirect'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return \Chilliapple\Redirect\Api\Data\RedirectInterface | null
     */
    private function getRedirect()
    {
        return $this->registry->registry('current_redirect');
    }

    /**
     * @return int|null
     */
    private function getRedirectId()
    {
        $redirect = $this->getRedirect();
        return ($redirect) ? $redirect->getId() : null;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl(
            '*/*/delete',
            [
                'redirect_id' => $this->getredirectId()
            ]
        );
    }
}
