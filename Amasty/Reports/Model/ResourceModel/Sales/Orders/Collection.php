<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Reports
 */


namespace Amasty\Reports\Model\ResourceModel\Sales\Orders;

use Amasty\Reports\Traits\Filters;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    use Filters;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\Reports\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\App\RequestInterface $request, // TODO move it out of here
        \Amasty\Reports\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $coreResourceHelper,
            $connection,
            $resource
        );
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @param \Amasty\Reports\Model\ResourceModel\Sales\Orders\Grid\Collection $collection
     */
    public function prepareCollection($collection)
    {
        $this->applyBaseFilters($collection);
        $this->applyToolbarFilters($collection);
    }

    /**
     * @param $collection
     */
    public function applyBaseFilters($collection)
    {
        $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'total_orders' => 'COUNT(entity_id)',
                'total_items' => 'SUM(total_item_count)',
                'subtotal' => 'SUM(base_subtotal)',
                'tax' => 'SUM(base_tax_amount)',
                'status' => 'status',
                'shipping' => 'SUM(base_shipping_amount)',
                'discounts' => 'SUM(base_discount_amount)',
                'total' => 'SUM(base_grand_total)',
                'invoiced' => 'IFNULL(SUM(base_total_invoiced), 0)',
                'refunded' => 'IFNULL(SUM(base_total_refunded), 0)',
                'entity_id' => 'CONCAT(entity_id,\''.$this->createUniqueEntity().'\')'
            ])
        ;
    }

    /**
     * @param $collection
     */
    public function applyToolbarFilters($collection)
    {
        $this->addFromFilter($collection);
        $this->addToFilter($collection);
        $this->addStoreFilter($collection);
        $this->addGroupFilter($collection);
        $this->addStatusFilter($collection);
    }

    /**
     * @param $collection
     */
    protected function addGroupFilter($collection)
    {
        $filters = $this->request->getParam('amreports');
        $group = isset($filters['type']) ? $filters['type'] : 'overview';
        switch ($group) {
            case 'overview':
                $collection->getSelect()
                    ->columns([
                        'period' => "DATE(created_at)",
                    ]);
                $collection->getSelect()->group('DATE(created_at)');
                break;
            case 'status':
                $collection->getSelect()->columns([
                    'period' => "status",
                ]);
                $collection->getSelect()->group('status');
                break;
        }
    }
}
