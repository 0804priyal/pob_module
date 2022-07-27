<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Source;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\GovernmentListRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;

class Government implements OptionSourceInterface
{
    /**
     * @var GovernmentListRepositoryInterface
     */
    private $repository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var array
     */
    private $options;

    /**
     * Government constructor.
     * @param GovernmentListRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GovernmentListRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = array_map(
                function (GovernmentInterface $government) {
                    return [
                        'label' => $government->getTitle(),
                        'value' => $government->getGovernmentId()
                    ];
                },
                $this->repository->getList($this->searchCriteriaBuilder->create())->getItems()
            );
            uasort(
                $this->options,
                function (array $optionA, array $optionB) {
                    return strcmp($optionA['label'], $optionB['label']);
                }
            );
            $this->options = array_values($this->options);
        }
        return $this->options;
    }
}
