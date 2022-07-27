<?php
namespace Chilliapple\Redirect\Api;

use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface RedirectRepositoryInterface
{
    /**
     * @param RedirectInterface $redirect
     * @return RedirectInterface
     */
    public function save(RedirectInterface $redirect);

    /**
     * @param $id
     * @return RedirectInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Chilliapple\Redirect\Api\Data\RedirectSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param RedirectInterface $redirect
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(RedirectInterface $redirect);

    /**
     * @param int $redirectId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($redirectId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
