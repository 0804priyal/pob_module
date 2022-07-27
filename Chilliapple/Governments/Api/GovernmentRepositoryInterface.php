<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Api;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface GovernmentRepositoryInterface
{
    /**
     * @param GovernmentInterface $government
     * @return GovernmentInterface
     */
    public function save(GovernmentInterface $government);

    /**
     * @param int $governmentId
     * @return GovernmentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $governmentId);

    /**
     * @param GovernmentInterface $government
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(GovernmentInterface $government);

    /**
     * @param int $governmentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $governmentId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
