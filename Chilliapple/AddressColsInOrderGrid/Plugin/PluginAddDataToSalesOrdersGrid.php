<?php

namespace Chilliapple\AddressColsInOrderGrid\Plugin;

/**
 * Class PluginAddDataToSalesOrdersGrid
 */
class PluginAddDataToSalesOrdersGrid
{
    /**
     * Execute after the getReport function to
     * Append additional data to sales order grid
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $resultCollection
     * @param $requestName
     * @return mixed
     */
    public function afterGetReport($subject, $resultCollection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $resultCollection;
        }
        if ($resultCollection->getMainTable() === $resultCollection->getResource()->getTable('sales_order_grid')) {
            try {
                $orderAddressTableName = $resultCollection->getResource()->getTable('sales_order_address');
                $resultCollection->getSelect()->joinLeft(
                    ['soa' => $orderAddressTableName],
                    'soa.parent_id = main_table.entity_id AND soa.address_type = \'shipping\'',
                    ['SUBSTRING_INDEX(soa.street,"\n",1) as address1','SUBSTR(soa.street,LENGTH(SUBSTRING_INDEX(soa.street, "\n", 1))+1) as address2','soa.city','soa.city','soa.postcode','soa.region','soa.country_id']
                );
            } catch (Exception $e) {
                // Not required to log and added the try catch to make sure
                // flow is not breaking while processing the report
            }
        }
        return $resultCollection;
    }
}
