<?php

declare(strict_types=1);

namespace Chilliapple\Governments\ViewModel\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Url implements ArgumentInterface
{
    /**
     * url builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getListUrl()
    {
        return $this->urlBuilder->getUrl('governments/government/index');
    }

    /**
     * @param GovernmentInterface $government
     * @return string
     */
    public function getGovernmentUrl(GovernmentInterface $government)
    {
        return $this->getGovernmentUrlById((int)$government->getId());
    }

    /**
     * @param int $id
     * @return string
     */
    public function getGovernmentUrlById(int $id)
    {
        return $this->urlBuilder->getUrl('governments/government/view', ['id' => $id]);
    }
}
