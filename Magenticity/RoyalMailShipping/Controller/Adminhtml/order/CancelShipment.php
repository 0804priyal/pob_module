<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class CancelShipment extends \Magento\Framework\App\Action\Action
{
    protected $_orderFactory;
    protected $_directoryList;
    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ResourceConnection $ResourceConnection,
        \Magento\Framework\App\DeploymentConfig $developConfig
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_directoryList = $directoryList;
        $this->resourceConnection = $ResourceConnection;
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

        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order = $this->_orderFactory->create()->load($orderId);
            if ($order->getShipmentsCollection()) {
                $ShipmentCollection = $order->getShipmentsCollection();
                if (!empty($ShipmentCollection)) {
                    foreach($ShipmentCollection as $shipmentData) {
                        $RoyalTrackingNumber = $shipmentData->getRmTrackingNumber();
                    }
                    if (!empty($RoyalTrackingNumber)) {
                        $OrderNumber = $order->getIncrementId();
                        $transactionId = $OrderNumber.uniqid();
                        $rmClass = new \royalmailDevelopment($transactionId);
                        $shipmentNumber = $RoyalTrackingNumber;
                        try {
                            $response = $rmClass->CancelShipment($transactionId,$shipmentNumber);
                            if (isset($response->integrationFooter->errors->error->errorCode)) {
                                $errdesc = $response->integrationFooter->errors->error->errorDescription;
                                $this->messageManager->addError(__($errdesc));
                                return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                            }

                            if (isset($response->completedCancelInfo->status->status->statusCode->code)) {
                                if ($response->completedCancelInfo->status->status->statusCode->code == "Cancelled") {
                                    $ShipmentTable = "";
                                    if ($tablePrefix) {
                                        $ShipmentTable = $tablePrefix."sales_shipment";
                                    } else {
                                        $ShipmentTable = "sales_shipment";
                                    }
                                    $sqlCancel = "UPDATE {$ShipmentTable} SET cancel_royal_shipment='1' WHERE order_id='$orderId'";
                                    $connection->query($sqlCancel);
                                    $this->messageManager->addSuccess(__('Shipment was cancelled.'));
                                    return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                                }
                            }
                        }  catch (\Exception $e) {
                            $this->messageManager->addError(__($e->getMessage()));
                            return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                        }
                    }
                }
            }
        }
    }
}
