<?php

declare(strict_types=1);

namespace Chilliapple\Governments\ViewModel\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Model\ResourceModel\Government\Collection;
use Chilliapple\Governments\Model\ResourceModel\Government\CollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Pager;

class ListGovernment implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var Collection[]
     */
    private $governmentsCollection;
    /**
     * @var Pager[]
     */
    private $pagers = [];
    /**
     * @var string
     */
    private $label = '';
    /**
     * @var array
     */
    private $filters = [];
    /**
     * @var string
     */
    private $pageVarName = '';
    /**
     * @var string
     */
    private $pageLimitVarName = '';
    /**
     * @var string
     */
    private $pageFragment = '';

    /**
     * ListGovernment constructor.
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        BlockFactory $blockFactory
    ) {
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->blockFactory = $blockFactory;
    }

    /**
     * prepare collection and pager
     * @throws NoSuchEntityException
     */
    private function processCollection()
    {
        $hash = $this->getHash();
        if (!isset($this->governmentCollections[$hash])) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(GovernmentInterface::IS_ACTIVE, 1);
            foreach ($this->filters as $filter) {
                if (isset($filter['field']) && isset($filter['condition'])) {
                    $collection->addFieldToFilter($filter['field'], $filter['condition']);
                }
            }
            $collection->addStoreFilter($this->storeManager->getStore()->getId());
            $collection->setOrder(GovernmentInterface::TITLE, SortOrder::SORT_ASC);
            /** @var Pager $pager */
            $pager = $this->blockFactory->createBlock(Pager::class);
            $this->pageFragment && $pager->setFragment($this->pageFragment);
            $this->pageVarName && $pager->setPageVarName($this->pageVarName);
            $this->pageLimitVarName && $pager->setLimitVarName($this->pageLimitVarName);
            $this->governmentCollections[$hash] = $collection;
            //$pager->setCollection($this->governmentCollections[$hash]);
            $this->pagers[$hash] = $pager;
        }
    }
    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function getPageVarName(): string
    {
        return $this->pageVarName;
    }

    /**
     * @return string
     */
    public function getPageLimitVarName(): string
    {
        return $this->pageLimitVarName;
    }

    /**
     * @return string
     */
    public function getPageFragment(): string
    {
        return $this->pageFragment;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @param string $pageVarName
     */
    public function setPageVarName(string $pageVarName): void
    {
        $this->pageVarName = $pageVarName;
    }

    /**
     * @param string $pageLimitVarName
     */
    public function setPageLimitVarName(string $pageLimitVarName): void
    {
        $this->pageLimitVarName = $pageLimitVarName;
    }

    /**
     * @param string $pageFragment
     */
    public function setPageFragment(string $pageFragment): void
    {
        $this->pageFragment = $pageFragment;
    }

    /**
     * string
     */
    private function getHash()
    {
        sort($this->filters);
        $keys = [
            'label' => $this->label,
            'filters' => $this->filters,
            'page_var_name' => $this->pageVarName,
            'page_limit_var_name' => $this->pageLimitVarName,
            'page_fragment' => $this->pageFragment
        ];
        return hash('md5', json_encode($keys));
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCollection()
    {
        $this->processCollection();
        $hash = $this->getHash();
        return $this->governmentCollections[$hash];
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPagerHtml()
    {
        $hash = $this->getHash();
        $this->processCollection();
        return $this->pagers[$hash]->toHtml();
    }
}
