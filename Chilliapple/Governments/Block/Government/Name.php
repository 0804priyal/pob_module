<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Block\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\ViewModel\Government\Url;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Name extends Template implements BlockInterface
{
    /**
     * @var GovernmentRepositoryInterface
     */
    private $repository;
    /**
     * @var GovernmentInterface
     */
    private $governments = [];
    /**
     * @var Url
     */
    private $url;

    /**
     * Link constructor.
     * @param Template\Context $context
     * @param Url $url
     * @param GovernmentRepositoryInterface $repository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Url $url,
        GovernmentRepositoryInterface $repository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->repository = $repository;
        $this->url = $url;
    }

    /**
     * @return bool|GovernmentInterface
     */
    public function getGovernment()
    {
        $governmentId = (int)$this->getData('government_id');
        if (!array_key_exists($governmentId, $this->governments)) {
            try {
                $government = $this->repository->get($governmentId);
                if (!$government->getIsActive()) {
                    $this->governments[$governmentId] = false;
                    return false;
                }
                $storeIds = [0, $this->_storeManager->getStore()->getId()];
                if (count(array_intersect($government->getStoreId(), $storeIds)) === 0) {
                    $this->governments[$governmentId] = false;
                    return false;
                }
                $this->governments[$governmentId] = $government;
                return $government;
            } catch (NoSuchEntityException $e) {
                $this->governments[$governmentId] = false;
                return false;
            }
        }
        return $this->governments[$governmentId];
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $info = parent::getCacheKeyInfo();
        $info[] = $this->getData('government_id');
        return $info;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData('label');
    }
    /**
     * @return string
     */
    public function getGovernmentUrl()
    {
        $government = $this->getGovernment();
        return $government ? $this->url->getGovernmentUrl($government) : '';
    }
}
