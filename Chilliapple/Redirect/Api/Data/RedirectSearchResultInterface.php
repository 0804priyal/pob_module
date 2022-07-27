<?php
namespace Chilliapple\Redirect\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface RedirectSearchResultInterface
{
    /**
     * get items
     *
     * @return \Chilliapple\Redirect\Api\Data\RedirectInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Chilliapple\Redirect\Api\Data\RedirectInterface[] $items
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
