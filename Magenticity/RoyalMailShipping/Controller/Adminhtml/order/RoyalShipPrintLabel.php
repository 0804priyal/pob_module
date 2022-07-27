<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class RoyalShipPrintLabel extends \Magento\Framework\App\Action\Action
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

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
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
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection
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
        $ShipmentFlag = "";
        $ShipmentEnhancement = "";
        $SignatureOfferingDetails = array();
        $signature = "";
        $AlreadyShipment = "";
        $shipment = "";
        $ShipmentEntityId = "";
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
        if (isset($Params['shipment_flag'])) {
            $ShipmentFlag = $Params['shipment_flag'];
        }
        if (isset($Params['shipment_entity_id'])) {
            $ShipmentEntityId = $Params['shipment_entity_id'];
        } else {
            $ShipmentEntityId = $this->getRequest()->getParam('shipment_id');
        }
        $SignatureOffering = $this->dataHelper->SignatureOffering();
        if (!empty($SignatureOffering)) {
            $SignatureOfferingDetails = explode(",", $SignatureOffering);
        }

        if (isset($Params['current_page_url'])) {
            $CurrentPageUrl = $Params['current_page_url'];
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

        $royalclass = $this->_directoryList->getPath("app") . '/code/Magenticity/RoyalMailShipping/lib/includes/royalmailDevelopment.php';

        if (@file_exists($royalclass)) {
            include_once($royalclass);
            ob_start();
        }

        if (!empty($ShipmentEntityId)) {
            $shipment = $this->shipmentManager->load($ShipmentEntityId);
            if ($shipment) {
                $RoyalTrackingNumber = $shipment->getRmTrackingNumber();
                if (empty($RoyalTrackingNumber) && $ShipmentFlag) {
                    $shipmentIncrementId = "";
                    $shipmentIncrementId = $shipment->getIncrementId();

                    $CustomerEmail = '';
                    if ($shipment->getOrder()->getCustomerEmail()) {
                        $CustomerEmail = $shipment->getOrder()->getCustomerEmail();
                    }

                    $shippingMethod = '';
                    if ($shipment->getOrder()->getShippingMethod()) {
                        $shippingMethod = $shipment->getOrder()->getShippingMethod();
                    }

                    $TotalItems = '';
                    $TotalItems = (int)$shipment->getTotalQty();

                    $TotalWeight = '';
                    $weight = 0;
                    $items = $shipment->getAllItems();
                    foreach ($items as $item) {
                        $weight += ($item->getWeight() * $item->getQty());
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
                    $ShipmentWeight = $TotalWeight;
                    if ($ShipmentWeight < 1) {
                        $this->messageManager->addError(__("Order items have no weight."));
                        return $this->_redirect($CurrentPageUrl);
                    }

                    if ($shipment->getShippingAddress()) {
                        $shippingAddress = $shipment->getShippingAddress();
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

                    $contact['name'] = $ShippingFirstName." ".$ShippingLastName;
                    $contact['complementaryName'] = $company;
                    $contact['telephoneNumber'] = $Telephone;
                    $contact['electronicAddress'] = $CustomerEmail;

                    $address['buildingName'] = "";
                    $address['buildingNumber'] = "";
                    $address['addressLine1'] = $streetAddress1;
                    $address['addressLine2'] = $streetAddress2;
                    $address['addressLine3'] = '';
                    $address['postTown'] = $city_shipping;
                    $address['postcode'] = $postcode_shipping;
                    $address['countryCode'] = $countryShipping;

                    $items['weight'] = $ShipmentWeight;
                    $items['numberOfItems'] = $TotalItems;

                    $shipmentDetails['senderReference'] =  $shipmentIncrementId;
                    $shipmentDetails['serviceOccurence'] = 1;
                    $shipmentDetails['shipmentTypeCode'] = 'Delivery';
                    if (!empty($ShipmentEnhancement)) {
                        $shipmentDetails['enhancement_type_code'] = $ShipmentEnhancement;
                    }
                    if (!empty($signature)) {
                        $shipmentDetails['signature'] = $signature;
                    }

                    $date = date('Y-m-d');
                    $shipmentDetails['shippingDate'] = $date;
                    $shipmentDetails['contact'] = $contact;
                    $shipmentDetails['address'] = $address;
                    $shipmentDetails['items'] = $items;

                    $shipmentDetails['serviceTypeCode'] = $serviceType;
                    $shipmentDetails['serviceOfferingCode'] = $serviceOffering;
                    $shipmentDetails['serviceFormatCode'] = $containerType;

                    $transactionId = $shipmentIncrementId.uniqid();
                    $rmClass = new \royalmailDevelopment($transactionId);
                    $returnedShipmentOperation = $rmClass->createRoyalMailShipment($shipmentDetails);
                    if (isset($returnedShipmentOperation['response'])) {
                        $response = $returnedShipmentOperation['response'];
                    }
                    if (empty($response)) {
                        $this->messageManager->addError(__("Getting Empty Response From RoyalMail."));
                        return $this->_redirect($CurrentPageUrl);
                    }
                    if (isset($response->Body->Fault->detail->exceptionDetails->exceptionCode)) {
                        $AuthErrorCode = $response->Body->Fault->detail->exceptionDetails->exceptionCode;
                        if($AuthErrorCode == 'E0007'){
                            $this->messageManager->addError(__('Authorization Failure.'));
                            return $this->_redirect($CurrentPageUrl);
                        }
                    }
                    if (isset($response->Body->createShipmentResponse->integrationFooter->errors->error)) {
                        $errorCode = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorCode;
                        $errdesc = $response->Body->createShipmentResponse->integrationFooter->errors->error->errorDescription;
                        if ($errorCode == 'E1001') {
                            $this->messageManager->addError(__('Postcode is invalid.'));
                            return $this->_redirect($CurrentPageUrl);
                        }
                        $this->messageManager->addError(__($errdesc));
                        return $this->_redirect($CurrentPageUrl);
                    }
                    if (isset($returnedShipmentOperation['shipmentNumber'])) {
                        $shipmentNumber = $returnedShipmentOperation['shipmentNumber'];
                        if (!empty($shipmentNumber)) {
                            $orderEntityId = $shipment->getOrderId();
                            $shipment->setRmTrackingNumber(trim($shipmentNumber));
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
                            if ($ShipmentFlag == "shipmentonly") {
                                $this->messageManager->addSuccess(__('Shipment Generated Successfully.'));
                                return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                            }
                        }
                    }
                } else {
                    if ($ShipmentFlag == "shipmentonly") {
                        $this->messageManager->addError(__('Shipment was already generated.'));
                        return $this->resultRedirectFactory->create()->setPath($CurrentPageUrl);
                    }
                    $shipmentNumber = $RoyalTrackingNumber;
                    $shipmentIncrementId = $shipment->getIncrementId();
                    $transactionId = $shipmentIncrementId.uniqid();
                    $rmClass = new \royalmailDevelopment($transactionId);
                }
                if (!empty($shipmentNumber)) {
                    $connectionDetails = $rmClass->createRoyalMailSoap();
                    $rmConnect = $connectionDetails['rmConnect'];
                    $xml_array = $connectionDetails['xml_array'];
                    $xml_array['shipmentNumber'] = $shipmentNumber;
                    $xml_array['outputFormat']   = "PDF";
                    $response = $rmConnect->printLabel($xml_array);
                    $response = $rmClass->parseSoapResponse($rmConnect->__getLastResponse());
                    $errors = $rmClass->returnWarnings($response);

                    if (isset($response->Body->printLabelResponse->integrationFooter->errors->error)) {
                        $errorCode = $response->Body->printLabelResponse->integrationFooter->errors->error->errorCode;
                        if ($errorCode == 'E1125') {
                            $errdesc = $response->Body->printLabelResponse->integrationFooter->errors->error->errorDescription;
                            $this->messageManager->addError(__($errdesc));
                           return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                        }
                        $errdesc = $response->Body->printLabelResponse->integrationFooter->errors->error->errorDescription;
                        $this->messageManager->addError(__($errdesc));
                        return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
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
                    return $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
                }
            }
        }
    }
}