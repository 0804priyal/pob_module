<?php
namespace Chilliapple\Redirect\Model\Redirect;

use Chilliapple\Redirect\Ui\Provider\CollectionProviderInterface;
use Magento\Ui\Component\MassAction\Filter;
use Chilliapple\Redirect\Model\ResourceModel\Redirect\CollectionFactory;

class CollectionProvider implements CollectionProviderInterface
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CollectionRetriever constructor.
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getCollection()
    {
        return $this->filter->getCollection($this->collectionFactory->create());
    }
}
