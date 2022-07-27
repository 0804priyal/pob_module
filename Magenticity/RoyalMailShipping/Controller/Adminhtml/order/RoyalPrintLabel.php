<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class RoyalPrintLabel extends \Magento\Framework\App\Action\Action
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
    protected $resultRawFactory;
    protected $dataHelper;
    protected $ServiceIntegrationCollection;
    protected $developConfig;

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
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magenticity\RoyalMailShipping\Helper\Data $dataHelper,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection,
        \Magento\Framework\App\DeploymentConfig $developConfig
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
        $this->resultRawFactory = $resultRawFactory;
        $this->dataHelper = $dataHelper;
        $this->ServiceIntegrationCollection = $ServiceIntegrationCollection;
        $this->developConfig = $developConfig;
    }

    public function execute()
    {
        $connection =  $this->resourceConnection->getConnection();
        $tablePrefix = $this->developConfig->get('db/table_prefix');
        $Params = $this->getRequest()->getPostValue();
        $serviceType = "";
        $serviceOffering = "";
        $containerType = "";
        $notifyCustomer = "";
        $selectedOrder = "";
        $orderEntityId = "";
        $ShipmentFlag = "";
        $ShipmentEnhancement = "";
        $SignatureOfferingDetails = array();
        $signature = "";
        $AlreadyShipment = "";
        $shipment = "";
        $ShipmentIncrementId = "";
        $CurrentPageUrl = "";

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
        if (isset($Params['service_enhancement_type'])) {
            $ShipmentEnhancement = $Params['service_enhancement_type'];
        }

        if (isset($Params['current_page_url'])) {
            $CurrentPageUrl = $Params['current_page_url'];
        }

        $SignatureOffering = $this->dataHelper->SignatureOffering();
        if (!empty($SignatureOffering)) {
            $SignatureOfferingDetails = explode(",", $SignatureOffering);
        }
        if (!empty($SignatureOfferingDetails) && $this->dataHelper->IsSignatureOffering()) {
            if (in_array($serviceOffering, $SignatureOfferingDetails)) {
                $ServiceSignature = $this->ServiceIntegrationCollection->create()->addFieldToSelect('signature')->addFieldToFilter('service_type', $serviceType)->addFieldToFilter('service_offering', $serviceOffering)->addFieldToFilter('service_format', $containerType)->addFieldToFilter('enhancement_type', $ShipmentEnhancement);
                $signatureData = $ServiceSignature->getFirstItem()->getData();

                if (isset($signatureData['signature'])) {
                    if ($signatureData['signature'] == "1") {
                        $signature = 1;
                    }
                }
            }
        }

        $orderId = $this->getRequest()->getParam('order_id');
        if (!empty($orderId)) {
            $orderEntityId = $orderId;
        }
        if (isset($Params['order_entity_id'])) {
            $orderEntityId = $Params['order_entity_id'];
        }
        if (isset($Params['shipment_flag'])) {
            $ShipmentFlag = $Params['shipment_flag'];
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
            if (!$order->hasShipments() && $ShipmentFlag) {
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
                        $ShipmentIncrementId = $shipment->getIncrementId();
                    } else {
                        $this->messageManager->addError(__("Order items have no weight."));
                        return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                    }
                }
            } else {
                if ($order->getShipmentsCollection()) {
                    $ShipmentCollection = $order->getShipmentsCollection();
                    foreach($ShipmentCollection as $shipmentData) {
                        $AlreadyShipment = $shipmentData;
                        $ShipmentIncrementId = $AlreadyShipment->getIncrementId();
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

            /*Get Total Weight Of Order*/
            $TotalWeight = '';
            $weight = 0;
            $items = $order->getAllVisibleItems();
            $num_of_shipped_items = 0;
            foreach ($items as $item) {
                $weight += ($item->getWeight() * $item->getQtyOrdered());
                $num_of_shipped_items += $item->getQtyShipped();
            }
            if (!empty($num_of_shipped_items)) {
                $TotalItems = (int)$num_of_shipped_items;
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
            $company = "";

            if (!empty($weight)) {
                $WeightUnit = $this->dataHelper->getWeightUnit();
                if ($WeightUnit == "lbs") {
                    $TotalWeight = $weight*453.592;
                }
                if ($WeightUnit == "kgs") {
                    $TotalWeight = $weight*1000;
                }
            }

            if ($order->getShipmentsCollection()) {
                $ShipmentCollection = $order->getShipmentsCollection();
                foreach($ShipmentCollection as $shipmentData) {
                    $RoyalTrackingNumber = $shipmentData->getRmTrackingNumber();
                }
            }

            /*if shipment is not generated in royalmail then first generate it and get tracking number from royalmail*/
            if (empty($RoyalTrackingNumber) && $ShipmentFlag) {
                $transactionId = $ShipmentIncrementId.uniqid();
                $rmClass = new \royalmailDevelopment($transactionId);

                /*prepare order weight details for pass it to royalmail api*/
                $orderWeight = $TotalWeight;
                if ($orderWeight < 1) {
                    $this->messageManager->addError(__("Order items have no weight."));
                    return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
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
                    $company = $shippingAddress->getCompany();

                    if (isset($street_shipping[0])) {
                        $streetAddress1 = $street_shipping[0];
                    }
                    if (isset($street_shipping[1])) {
                        $streetAddress2 = $street_shipping[1];
                    }
                }

                /*Get Conatct Details and pass that in royalmail api*/
                $contact['name'] = $ShippingFirstName." ".$ShippingLastName;
                $contact['complementaryName'] = $company;
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

                $shipmentDetails['senderReference'] = $ShipmentIncrementId;
                $shipmentDetails['serviceOccurence'] = 1;
                $shipmentDetails['shipmentTypeCode'] = 'Delivery';
                if (!empty($ShipmentEnhancement)) {
                    $shipmentDetails['enhancement_type_code'] = $ShipmentEnhancement;
                }
                if (!empty($signature)) {
                    $shipmentDetails['signature'] = $signature;
                }

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
                    return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                }
                if (isset($response->Body->Fault->detail->exceptionDetails->exceptionCode)) {
                    $AuthErrorCode = $response->Body->Fault->detail->exceptionDetails->exceptionCode;
                    if($AuthErrorCode == 'E0007'){
                        $this->messageManager->addError(__('Authorization Failure.'));
                        return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                    }
                }
                if (isset($response->Body->createShipmentResponse->integrationFooter->errors->error)) {
                    $errorCode = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorCode;
                    $errdesc = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorDescription;
                    if ($errorCode == 'E1001') {
                        $this->messageManager->addError(__('Postcode is invalid.'));
                        return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                    }
                    $this->messageManager->addError(__($errdesc));
                    return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                }

                if (isset($returnedShipmentOperation['shipmentNumber'])) {
                    $shipmentNumber = $returnedShipmentOperation['shipmentNumber'];
                    if (!empty($shipmentNumber)) {
                        $ShipmentTable = "";
                        if ($tablePrefix) {
                            $ShipmentTable = $tablePrefix."sales_shipment";
                        } else {
                            $ShipmentTable = "sales_shipment";
                        }
                        $sqltracking = "UPDATE {$ShipmentTable} SET rm_tracking_number='$shipmentNumber' WHERE order_id='$orderEntityId'";
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
                        } else {
                            $ShipmentTrackTable = "";
                            if ($tablePrefix) {
                                $ShipmentTrackTable = $tablePrefix."sales_shipment_track";
                            } else {
                                $ShipmentTrackTable = "sales_shipment_track";
                            }
                            $MagentoTrackNumber = $connection->fetchOne("SELECT track_number FROM {$ShipmentTrackTable} WHERE order_id='$orderEntityId'");
                            if (empty($MagentoTrackNumber)) {
                                $TrackingData = $connection->fetchAll("SELECT * FROM {$ShipmentTable} WHERE order_id='$orderEntityId'");
                                if ($TrackingData){
                                    foreach ($TrackingData as $trackingvalue) {
                                       $entityId = $trackingvalue['entity_id'];
                                       $orderId = $trackingvalue['order_id'];
                                       $Track_Number = $trackingvalue['rm_tracking_number'];
                                    }
                                }
                                if (!empty($Track_Number)) {
                                    $tableName = 'sales_shipment_track';
                                    $sql = "Insert Into " . $ShipmentTrackTable . " (parent_id, order_id, track_number, title, carrier_code) Values ($entityId,$orderId,'$Track_Number','RoyalMail','RoyalMail')";
                                    $connection->query($sql);
                                }
                            }
                            if (!empty($notifyCustomer) && !empty($AlreadyShipment)) {
                                $this->shipmentNotifier->notify($AlreadyShipment);
                            }
                        }
                        if ($ShipmentFlag == "shipmentonly") {
                            $this->messageManager->addSuccess(__('Shipment Generated Successfully.'));
                            return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                        }
                    }
                }
            } else {
                if ($ShipmentFlag == "shipmentonly") {
                    $this->messageManager->addError(__('Shipment was already generated.'));
                    return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                }
                /*get already generated tracking number*/
                $transactionId = $ShipmentIncrementId.uniqid();
                $rmClass = new \royalmailDevelopment($transactionId);
                $shipmentNumber = $RoyalTrackingNumber;
            }

            if (!empty($shipmentNumber)) {
                $connectionDetails = $rmClass->createRoyalMailSoap();
                $rmConnect = $connectionDetails['rmConnect'];
                $xml_array = $connectionDetails['xml_array'];
                $xml_array['shipmentNumber'] = $shipmentNumber;
                $xml_array['outputFormat']   = "PDF";
                $response = $rmConnect->printLabel($xml_array);

                if (!empty($response)) {
                    $response = $rmClass->parseSoapResponse($rmConnect->__getLastResponse());
                    $errors = $rmClass->returnWarnings($response);

                    if (isset($response->Body->printLabelResponse->integrationFooter->errors->error)) {
                        $errorCode = $response->Body->printLabelResponse->integrationFooter->errors->error->errorCode;
                        if ($errorCode == 'E1125') {
                            $errdesc = $response->Body->printLabelResponse->integrationFooter->errors->error->errorDescription;
                            $this->messageManager->addError(__($errdesc));
                            return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                        }
                        $errdesc = $response->Body->printLabelResponse->integrationFooter->errors->error->errorDescription;
                        $this->messageManager->addError(__($errdesc));
                        return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                    }

                    $label = $rmClass->returnLabel($response);
                    if ($label) {
                        $decodedlabel = base64_decode($label);
                        $filename = 'printedlabel-' . $shipmentNumber.".pdf";
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . strlen($decodedlabel));
                        ob_clean();
                        flush();
                        $resultRaw = $this->resultRawFactory->create();
                        $resultRaw->setContents($decodedlabel);
                        return $resultRaw;
                    }
                } else {
                    $this->messageManager->addError(__("Getting Empty Response From RoyalMail."));
                    return $this->resultRedirectFactory->create()->setPath('sales/order/', ['_current' => true]);
                }
            }
        }
    }
}
