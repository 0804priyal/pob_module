<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface GovernmentSearchResultInterface
{
    /**
     * get items
     *
     * @return \Chilliapple\Governments\Api\Data\GovernmentInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Chilliapple\Governments\Api\Data\GovernmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}
