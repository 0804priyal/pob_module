<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class RoyalShipCancelShipment extends \Magento\Framework\App\Action\Action
{
    protected $_orderFactory;
    protected $_directoryList;
    protected $resourceConnection;
    protected $shipmentManager;
    protected $developConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ResourceConnection $ResourceConnection,
        \Magento\Sales\Model\Order\Shipment $shipmentManager,
        \Magento\Framework\App\DeploymentConfig $developConfig
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_directoryList = $directoryList;
        $this->resourceConnection = $ResourceConnection;
        $this->shipmentManager = $shipmentManager;
        $this->developConfig = $developConfig;
    }

    public function execute() {
        $connection =  $this->resourceConnection->getConnection();
        $royalclass = $this->_directoryList->getPath("app") . '/code/Magenticity/RoyalMailShipping/lib/includes/royalmailDevelopment.php';
        $tablePrefix = $this->developConfig->get('db/table_prefix');

        if (@file_exists($royalclass)) {
            include_once($royalclass);
            ob_start();
        }

        $ShipmentEntityId = $this->getRequest()->getParam('shipment_id');
        if ($ShipmentEntityId) {
            $shipment = $this->shipmentManager->load($ShipmentEntityId);
            $RoyalTrackingNumber = $shipment->getRmTrackingNumber();

            if (!empty($RoyalTrackingNumber)) {
                $ShipmentIncrementId = $shipment->getIncrementId();
                $transactionId = $ShipmentIncrementId.uniqid();
                $rmClass = new \royalmailDevelopment($transactionId);
                $shipmentNumber = $RoyalTrackingNumber;
                try {
                    $response = $rmClass->CancelShipment($transactionId,$shipmentNumber);
                    if (isset($response->integrationFooter->errors->error->errorCode)) {
                        $errdesc = $response->integrationFooter->errors->error->errorDescription;
                        $this->messageManager->addError(__($errdesc));
                        return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                    }

                    if (isset($response->completedCancelInfo->status->status->statusCode->code)) {
                        if ($response->completedCancelInfo->status->status->statusCode->code == "Cancelled") {
                            $ShipmentTable = "";
                            if ($tablePrefix) {
                                $ShipmentTable = $tablePrefix."sales_shipment";
                            } else {
                                $ShipmentTable = "sales_shipment";
                            }
                            $sqlCancel = "UPDATE {$ShipmentTable} SET cancel_royal_shipment='1' WHERE entity_id='$ShipmentEntityId'";
                            $connection->query($sqlCancel);
                            $this->messageManager->addSuccess(__('Shipment was cancelled.'));
                            return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                        }
                    }
                }  catch (\Exception $e) {
                    $this->messageManager->addError(__($e->getMessage()));
                    return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                }
            }
        }
    }
}
