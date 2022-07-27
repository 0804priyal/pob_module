<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class GenerateShipment extends \Magento\Framework\App\Action\Action
{
     protected $orderManager;
     protected $_directoryList;
     protected $convertOrder;
     protected $trackFactory;
     protected $resourceConnection;
     protected $_downloader;
     protected $shipmentManager;
     protected $_filesystem;
     protected $shipmentNotifier;
     protected $_orderFactory;

     public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Order $orderManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\App\ResourceConnection $ResourceConnection,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Sales\Model\Order\Shipment $shipmentManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->orderManager = $orderManager;
        $this->_directoryList = $directoryList;
        $this->convertorder = $convertOrder;
        $this->trackFactory = $trackFactory;
        $this->resourceConnection = $ResourceConnection;
        $this->_downloader =  $fileFactory;
        $this->shipmentManager = $shipmentManager;
        $this->_filesystem = $filesystem;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->_orderFactory = $orderFactory;
    }

    public function execute()
    {
        $connection =  $this->resourceConnection->getConnection();
        $Params = $this->getRequest()->getPostValue();
        $serviceType = "";
        $serviceOffering = "";
        $containerType = "";
        $notifyCustomer = "";
        $selectedOrder = "";
        $orderEntityId = "";

        /*get selected details for services*/
        if (isset($Params['service_type'])) {
            $serviceType = $Params['service_type'];
        }
        if (isset($Params['service_offering'])) {
            $serviceOffering = $Params['service_offering'];
        }
        if (isset($Params['container_type'])) {
            $containerType = $Params['container_type'];
        }
        if (isset($Params['notify_customer'])) {
            $notifyCustomer = $Params['notify_customer'];
        }
        if (isset($Params['selected_order'])) {
            $selectedOrder = $Params['selected_order'];
        }
        if (isset($Params['order_entity_id'])) {
            $orderEntityId = $Params['order_entity_id'];
        }

        $royalclass = $this->_directoryList->getPath("app") . '/code/Magenticity/RoyalMailShipping/lib/includes/royalmailDevelopment.php';

        if (@file_exists($royalclass)) {
            include_once($royalclass);
            ob_start();
        }

        if ($orderEntityId) {
            $order = $this->_orderFactory->create()->load($orderEntityId);
            /*Get Shipping Method Name*/
            $shippingMethod = '';
            if ($order->getShippingMethod()) {
                $shippingMethod = $order->getShippingMethod();
            }

            /*Generate Magento Shipment if shipment is not generated*/
            if (!$order->hasShipments()) {
                /*Generate Shipment Dynamically*/
                $shipment = $this->convertorder->toShipment($order);
                $flag = '0';
                if ($order->getAllItems()) {
                    foreach ($order->getAllItems() AS $orderItem) {
                        /*Check if order item has qty to ship or is virtual*/
                        if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                            continue;
                        }
                        $qtyShipped = $orderItem->getQtyToShip();
                        /*Create shipment item with qty*/
                        if ($qtyShipped > 0) {
                            $shipmentItem = $this->convertorder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                            /*Add shipment item to shipment*/
                            $shipment->addItem($shipmentItem);
                            $flag = '1';
                        }
                    }
                    if ($flag == 1) {
                        /*Register shipment*/
                        $shipment->register();
                        $shipment->getOrder()->setIsInProcess(true);
                        /*save shipment*/
                        $shipment->save();
                        $shipment->getOrder()->save();
                    } else {
                        $this->messageManager->addError(__("We Can't Generate Empty Shipment"));
                        return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                    }
                }
            } else {
                if ($order->getShipmentsCollection()) {
                    $ShipmentCollection = $order->getShipmentsCollection();
                    foreach($ShipmentCollection as $shipmentData) {
                        $shipment = $shipmentData;
                    }
                }
            }

            /*get order details*/
            $OrderNumber = '';
            $OrderNumber = $order->getIncrementId();

            /*get Customer Email*/
            $CustomerEmail = '';
            if ($order->getCustomerEmail()) {
                $CustomerEmail = $order->getCustomerEmail();
            }

            /*Get Total Qty of order*/
            $TotalItems = '';
            $TotalItems = (int)$order->getData('total_qty_ordered');

            /*Get Total Weight Of Order*/
            $TotalWeight = '';
            $weight = 0;
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                $weight += ($item->getWeight() * $item->getQtyOrdered()) ;
            }

            $contact = array();
            $ShippingFirstName = "";
            $ShippingLastName = "";
            $Telephone = "";
            $address = array();
            $streetAddress1 = "";
            $streetAddress2 = "";
            $city_shipping = "";
            $postcode_shipping = "";
            $countryShipping = "";
            $shipmentDetails = array();
            $items = array();
            $shipmentNumber = "";
            $response = "";
            $RoyalTrackingNumber = "";

            $TotalWeight = $weight;

            if ($order->getShipmentsCollection()) {
                $ShipmentCollection = $order->getShipmentsCollection();
                foreach($ShipmentCollection as $shipmentData) {
                    $RoyalTrackingNumber = $shipmentData->getRmTrackingNumber();
                }
            }

            if (empty($RoyalTrackingNumber)) {
                $transactionId = $OrderNumber.uniqid();
                $rmClass = new \royalmailDevelopment($transactionId);

                /*prepare order weight details for pass it to royalmail api*/
                $orderWeight = $TotalWeight;
                if ($orderWeight < 1) {
                    $this->messageManager->addError(__("Order items have no weight."));
                    return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                }
                /*get shipping address details*/
                if ($order->getShippingAddress()) {
                    $shippingAddress = $order->getShippingAddress();
                    $Telephone = $shippingAddress->getTelephone();
                    $ShippingFirstName = $shippingAddress->getFirstName();
                    $ShippingLastName = $shippingAddress->getLastName();
                    $street_shipping = $shippingAddress->getStreet();
                    $city_shipping = $shippingAddress->getCity();
                    $postcode_shipping = $shippingAddress->getPostcode();
                    $countryShipping = $shippingAddress->getCountryId();

                    if (isset($street_shipping[0])) {
                        $streetAddress1 = $street_shipping[0];
                    }
                    if (isset($street_shipping[1])) {
                        $streetAddress2 = $street_shipping[1];
                    }
                }

                /*Get Conatct Details and pass that in royalmail api*/
                $contact['name'] = $shippingMethod." ".$ShippingFirstName." ".$ShippingLastName;
                $contact['complementaryName'] = "";
                $contact['telephoneNumber'] = $Telephone;
                $contact['electronicAddress'] = $CustomerEmail;

                /*pass address details to royalmail*/
                $address['buildingName'] = "";
                $address['buildingNumber'] = "";
                $address['addressLine1'] = $streetAddress1;
                $address['addressLine2'] = $streetAddress2;
                $address['addressLine3'] = '';
                $address['postTown'] = $city_shipping;
                $address['postcode'] = $postcode_shipping;
                $address['countryCode'] = $countryShipping;

                /*pass weight and item details*/
                $items['weight'] = $orderWeight;
                $items['numberOfItems'] = $TotalItems;

                $shipmentDetails['senderReference'] =  $OrderNumber;
                $shipmentDetails['serviceOccurence'] = 1;
                $shipmentDetails['shipmentTypeCode'] = 'Delivery';

                /* pass shipping date to royalmail*/
                $date = date('Y-m-d');
                $shipmentDetails['shippingDate'] = $date;
                $shipmentDetails['contact'] = $contact;
                $shipmentDetails['address'] = $address;
                $shipmentDetails['items'] = $items;

                /*pass service regarding details to royalmail*/
                $shipmentDetails['serviceTypeCode'] = $serviceType;
                $shipmentDetails['serviceOfferingCode'] = $serviceOffering;
                $shipmentDetails['serviceFormatCode'] = $containerType;

                /*Send api request for generate tracking number from royalmail api*/
                $returnedShipmentOperation = $rmClass->createRoyalMailShipment($shipmentDetails);
                if (isset($returnedShipmentOperation['response'])) {
                    $response = $returnedShipmentOperation['response'];
                }
                if (empty($response)) {
                    $this->messageManager->addError(__("Getting Empty Response From RoyalMail."));
                    return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                }
                if (isset($response->Body->Fault->detail->exceptionDetails->exceptionCode)) {
                    $AuthErrorCode = $response->Body->Fault->detail->exceptionDetails->exceptionCode;
                    if($AuthErrorCode == 'E0007'){
                        $this->messageManager->addError(__('Authorization Failure.'));
                        return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                    }
                }
                if (isset($response->Body->createShipmentResponse->integrationFooter->errors->error)) {
                    $errorCode = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorCode;
                    $errdesc = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorDescription;
                    if ($errorCode == 'E1001') {
                        $this->messageManager->addError(__('Postcode is invalid. Please contact main office'));
                        return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                    }
                    $this->messageManager->addError(__($errdesc));
                    return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                }

                if (isset($returnedShipmentOperation['shipmentNumber'])) {
                    $shipmentNumber = $returnedShipmentOperation['shipmentNumber'];
                    if (!empty($shipmentNumber)) {
                        $sqltracking = "UPDATE sales_shipment SET rm_tracking_number='$shipmentNumber' WHERE order_id='$orderEntityId'";
                        $connection->query($sqltracking);
                        /*save data in sales_shipment_track table*/
                        $data = array(
                            'carrier_code' => 'RoyalMail',
                            'title' => 'RoyalMail',
                            'number' => $shipmentNumber,
                        );
                        $track = $this->trackFactory->create()->addData($data);
                        if ($shipment) {
                            $shipment->addTrack($track);
                            if (!empty($notifyCustomer)) {
                                $this->shipmentNotifier->notify($shipment);
                            }
                            $shipment->save();
                        }
                        $this->messageManager->addSuccess(__('Shipment Generated Successfully.'));
                        return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                    }
                }
            } else {
                $this->messageManager->addError(__('Shipment was already generated.'));
                return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
            }
        }
    }
}
