<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Api;

use Chilliapple\Governments\Api\Data\GovernmentSearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface GovernmentListRepositoryInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return GovernmentSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
