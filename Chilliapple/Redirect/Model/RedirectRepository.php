<?php
namespace Chilliapple\Redirect\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterfaceFactory;
use Chilliapple\Redirect\Api\Data\RedirectSearchResultInterfaceFactory;
use Chilliapple\Redirect\Api\RedirectRepositoryInterface;
use Chilliapple\Redirect\Model\ResourceModel\Redirect as RedirectResourceModel;
use Chilliapple\Redirect\Model\ResourceModel\Redirect\Collection;
use Chilliapple\Redirect\Model\ResourceModel\Redirect\CollectionFactory as RedirectCollectionFactory;

class RedirectRepository implements RedirectRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Redirect resource model
     *
     * @var RedirectResourceModel
     */
    protected $resource;

    /**
     * Redirect collection factory
     *
     * @var RedirectCollectionFactory
     */
    protected $redirectCollectionFactory;

    /**
     * Redirect interface factory
     *
     * @var RedirectInterfaceFactory
     */
    protected $redirectInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var RedirectSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param RedirectResourceModel $resource
     * @param RedirectCollectionFactory $redirectCollectionFactory
     * @param RedirectnterfaceFactory $redirectInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param RedirectSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        RedirectResourceModel $resource,
        RedirectCollectionFactory $redirectCollectionFactory,
        RedirectInterfaceFactory $redirectInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        RedirectSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->redirectCollectionFactory = $redirectCollectionFactory;
        $this->redirectInterfaceFactory  = $redirectInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Redirect.
     *
     * @param \Chilliapple\Redirect\Api\Data\RedirectInterface $redirect
     * @return \Chilliapple\Redirect\Api\Data\RedirectInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(RedirectInterface $redirect)
    {
        /** @var RedirectInterface|\Magento\Framework\Model\AbstractModel $redirect */
        try {
            $this->resource->save($redirect);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Redirect: %1',
                $exception->getMessage()
            ));
        }
        return $redirect;
    }

    /**
     * Retrieve Redirect
     *
     * @param int $redirectId
     * @return \Chilliapple\Redirect\Api\Data\RedirectInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($redirectId)
    {
        if (!isset($this->instances[$redirectId])) {
            /** @var RedirectInterface|\Magento\Framework\Model\AbstractModel $redirect */
            $redirect = $this->redirectInterfaceFactory->create();
            $this->resource->load($redirect, $redirectId);
            if (!$redirect->getId()) {
                throw new NoSuchEntityException(__('Requested Redirect doesn\'t exist'));
            }
            $this->instances[$redirectId] = $redirect;
        }
        return $this->instances[$redirectId];
    }

    /**
     * Retrieve Redirects matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Chilliapple\Redirect\Api\Data\RedirectSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Chilliapple\Redirect\Api\Data\RedirectSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Chilliapple\Redirect\Model\ResourceModel\Redirect\Collection $collection */
        $collection = $this->redirectCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            $collection->addOrder('main_table.' . RedirectInterface::REDIRECT_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var RedirectInterface[] $redirects */
        $redirects = [];
        /** @var \Chilliapple\Redirect\Model\Redirect $redirect */
        foreach ($collection as $redirect) {
            /** @var RedirectInterface $redirectDataObject */
            $redirectDataObject = $this->redirectInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $redirectDataObject,
                $redirect->getData(),
                RedirectInterface::class
            );
            $redirects[] = $redirectDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($redirects);
    }

    /**
     * Delete Redirect
     *
     * @param RedirectInterface $redirect
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(RedirectInterface $redirect)
    {
        /** @var RedirectInterface|\Magento\Framework\Model\AbstractModel $redirect */
        $id = $redirect->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($redirect);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removeRedirect %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Redirect by ID.
     *
     * @param int $redirectId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($redirectId)
    {
        $redirect = $this->get($redirectId);
        return $this->delete($redirect);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * clear caches instances
     * @return void
     */
    public function clear()
    {
        $this->instances = [];
    }
}
