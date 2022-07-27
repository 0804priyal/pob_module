<?php
	// @codingStandardsIgnoreStart
	class WsseAuthHeader extends SoapHeader{
		// @codingStandardsIgnoreEnd
		private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
		private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

	   function __construct($user, $pass, $nonce, $created){
		$auth           = new stdClass();
	    $auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
	    $auth->Password = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
	    $auth->Nonce    = new SoapVar($nonce, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
	    $auth->Created  = new SoapVar($created, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wsu_ns);
	    $username_token = new stdClass();
	    $username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);
	    $security_sv = new SoapVar(
	    new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),            SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
	    parent::__construct($this->wss_ns, 'Security', $security_sv, true);
	    }
	}

	// @codingStandardsIgnoreStart
	class integrationHeader
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($applicationId,$transactionId) {
			$this->wrapper = array();
			$this->wrapper['dateTime'] = date('c');
			$this->wrapper['identification']['applicationId'] = $applicationId;
			$this->wrapper['identification']['transactionId'] = $transactionId;
			$this->wrapper['version']	= 2;
		}
	}

	// @codingStandardsIgnoreStart
	class requestedShipment
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($shipmentTypeCode, $serviceOccurence, $serviceTypeCode, $serviceOfferingCode, $serviceFormatCode, $senderReference, $shippingDate, $recipientAddress, $items, $recipientContact) {
			$this->wrapper = array();
			$this->wrapper['shipmentType']		= array('code'=>$shipmentTypeCode);
			$this->wrapper['serviceOccurrence']	= $serviceOccurence;
			$this->wrapper['serviceType']		= array('code'=>$serviceTypeCode);
			$this->wrapper['serviceOffering']	= array('serviceOfferingCode' => array('code'=>$serviceOfferingCode));
			if ($serviceFormatCode!="") {
				$this->wrapper['serviceFormat']	= array('serviceFormatCode'=>array('code'=>$serviceFormatCode));
			}
			$this->wrapper['senderReference']	= $senderReference;
			$this->wrapper['shippingDate']		= $shippingDate;
			$this->wrapper['recipientContact']  = $recipientContact->wrapper;
			$this->wrapper['recipientAddress']  = $recipientAddress->wrapper;
			if ($serviceTypeCode == "I") {
				$this->wrapper['internationalInfo'] = $items->wrapper;
			} else {
				$this->wrapper['items']  			= $items->wrapper;
			}
		}
	}

	// @codingStandardsIgnoreStart
	class recipientContact
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($details) {
			$this->wrapper = array();
			$this->wrapper['name'] 				= $details['name'];
			$this->wrapper['complementaryName'] = $details['complementaryName'];
			$this->wrapper['telephoneNumber']   = array('telephoneNumber' => $details['telephoneNumber']);
			$this->wrapper['electronicAddress'] = array('electronicAddress' => $details['electronicAddress']);
		}
	}

	// @codingStandardsIgnoreStart
	class recipientAddress
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($details) {
			$this->wrapper = array();
			$this->wrapper['buildingName'] 		= $details['buildingName'];
			$this->wrapper['buildingNumber'] 	= $details['buildingNumber'];
			$this->wrapper['addressLine1']  	= $details['addressLine1'];
			$this->wrapper['addressLine2']  	= $details['addressLine2'];
			$this->wrapper['addressLine3']  	= $details['addressLine3'];
			$this->wrapper['postTown']  		= $details['postTown'];
			$this->wrapper['postcode']  		= $details['postcode'];
			$this->wrapper['country'] 			= array('countryCode'=> array('code'=>strtoupper($details['countryCode'])));
		}
	}

	// @codingStandardsIgnoreStart
	class items
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($details) {
			$this->wrapper = array();
			$this->wrapper['item'] = array('numberOfItems' => array($details['numberOfItems']));
			$this->wrapper['item']['weight'] = array('value' => $details['weight'], 'unitOfMeasure' => array('unitOfMeasureCode'=>array('code'=>'g')));
		}
	}

	// @codingStandardsIgnoreStart
	class internationalInfo
	{
		// @codingStandardsIgnoreEnd
		public $wrapper;
		public function __construct($details) {
			$this->wrapper = array();
			$this->wrapper['parcels'] = array('parcel' => array('weight' => array('unitOfMeasure' => array('unitOfMeasureCode' => array('code' => 'g')), 'value' => $details['weight'])));
			$this->wrapper['parcels']['contentDetails'] = array('unitQuantity' => '1');
		}
	}

// @codingStandardsIgnoreStart
class royalmailDevelopment
{
	// @codingStandardsIgnoreEnd
	private $Password;
	private $Username;
	private $applicationId;
	private $transactionId;
	private $clientId;
	private $clientSecret;
	private $rmClass;
	private $wsdl;
	public $trace;
	public $exceptions;

	public function __construct($transactionId = "") {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
		$rootPath  =  $directory->getPath("app");
		$Helper = $objectManager->create('\Magenticity\RoyalMailShipping\Helper\Data');
		$ApiUserName = $Helper->getApiUserName();
		$ApiPassword = $Helper->getApiPassword();
		$ApiApplicationId = $Helper->getApiApplicationId();
		$ApiClientId = $Helper->getApiClientId();
		$ApiClientSecret = $Helper->getApiClientSecret();

		$this->wsdl = $rootPath."/code/Magenticity/RoyalMailShipping/lib/ShippingAPI_V2_0_9.wsdl";
		$this->Password = $ApiPassword;
		$this->Username = $ApiUserName;
		$this->applicationId = $ApiApplicationId;
		$this->transactionId = $transactionId;
		$this->trace = true;
		$this->exceptions = true;
		$this->clientId = $ApiClientId;
		$this->clientSecret = $ApiClientSecret;
		$this->serviceOccurence = 1;
	}

	public function createRoyalMailShipment($shipmentDetails) {
		$warnings = array();
		$integrationNotices = array();
		$connectionDetails = $this->createRoyalMailSoap();
		$rmConnect = "";
		$xml_array = "";
		$requestedShipment = "";

		if (isset($connectionDetails['rmConnect'])) {
			$rmConnect = $connectionDetails['rmConnect'];
		}
		if (isset($connectionDetails['xml_array'])) {
			$xml_array = $connectionDetails['xml_array'];
		}

		if (!empty($shipmentDetails)) {
			$recipientAddress 	= new recipientAddress($shipmentDetails['address']);
			$recipientContact 	= new recipientContact($shipmentDetails['contact']);
			$itemElement 		= new items($shipmentDetails['items']);

			$EnhancementTypeCode = "";
			$IsSignature = "";
			if (isset($shipmentDetails['enhancement_type_code'])) {
				$EnhancementTypeCode =  $shipmentDetails['enhancement_type_code'];
			}
			if (isset($shipmentDetails['signature'])) {
				$IsSignature =  $shipmentDetails['signature'];
			}

			if ($shipmentDetails['serviceTypeCode'] == "I") {
				$itemElement = new internationalInfo($shipmentDetails['items']);
			}

			$requestedShipment 	= new requestedShipment(
				$shipmentDetails['shipmentTypeCode'],
				$shipmentDetails['serviceOccurence'],
				$shipmentDetails['serviceTypeCode'],
				$shipmentDetails['serviceOfferingCode'],
				$shipmentDetails['serviceFormatCode'],
				$shipmentDetails['senderReference'],
				$shipmentDetails['shippingDate'],
				$recipientAddress,
				$itemElement,
				$recipientContact
			);
		}

		if (!empty($requestedShipment)) {
			$xml_array['requestedShipment'] = $requestedShipment->wrapper;
			if (!empty($EnhancementTypeCode)) {
				$xml_array['requestedShipment']['serviceEnhancements'] = array('enhancementType' => array('serviceEnhancementCode' => array('code' => $EnhancementTypeCode)));
			}
			if (!empty($IsSignature)) {
				$xml_array['requestedShipment']['signature'] = '1';
			}
			try {
				$response = $rmConnect->createShipment($xml_array);
			} catch (\Exception $e) {
				return $e->getMessage();
			}
			$response 						= $this->parseSoapResponse($rmConnect->__getLastResponse());
			$warnings 						= $this->returnWarnings($response);
			$integrationNotices 			= array_merge($integrationNotices, $warnings);
			$shipmentNumber					= $this->returnShipmentNumber($response);
			$returnArray 					= array("response"=>$response, "shipmentNumber"=>$shipmentNumber, "integrationNotices"=>$integrationNotices);
			return $returnArray;
		}
		$response = $rmClass->parseSoapResponse($rmConnect->__getLastResponse());
		$errors = $rmClass->returnException($response);
		$rmClass->errorResponse($errors);
	}

	public function createRoyalMailSoap() {
		try {
			$rmConnect = new SoapClient(
				$this->wsdl, array(
					'trace' => $this->trace,
					'exceptions' => $this->exceptions,
					'stream_context' => stream_context_create(
						array(
						'http' =>
							array(
								'header'           => implode(
									"\r\n",array(
										'Accept: application/soap+xml',
										'X-IBM-Client-Id: ' . $this->clientId,
										'X-IBM-Client-Secret: ' . $this->clientSecret,
									)
								),
							),
						)
					)
				)
			);

			$Nonce = mt_rand();
			$Created = gmdate('Y-m-d\TH:i:s\Z');
			$nonce_date_pwd = pack("A*",$Nonce) . pack("A*",$Created) . pack("H*", sha1($this->Password));
			$passwordDigest = base64_encode(pack('H*',	sha1($nonce_date_pwd)));
			$Nonce = base64_encode($Nonce);
			$wsSecurityHeader = new WsseAuthHeader($this->Username, $passwordDigest, $Nonce, $Created);
			$rmConnect->__setSoapHeaders($wsSecurityHeader);
			$xml_array = array();
			$integrationHeader = new integrationHeader($this->applicationId, $this->transactionId);
			$xml_array['integrationHeader'] = $integrationHeader->wrapper;
			$returnArray = array();
			$returnArray['xml_array'] = $xml_array;
			$returnArray['rmConnect'] = $rmConnect;
			return $returnArray;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	function parseSoapResponse($response) {
		$xml = str_replace('soapenv:Envelope', 'Envelope', $response);
		$xml = str_replace('soapenv:Body', 'Body', $xml);
		$xml = simplexml_load_string($xml);
		return $xml;
	}

	function returnShipmentNumber($parsedResponse) {
		$DataparsedResponse = $parsedResponse;
		if (!isset($parsedResponse->Body->createShipmentResponse->integrationFooter->errors->error)){
			if (isset($parsedResponse->Body->createShipmentResponse->completedShipmentInfo->allCompletedShipments->completedShipments->shipments->shipmentNumber[0])) {
				$DataparsedResponse = $parsedResponse->Body->createShipmentResponse->completedShipmentInfo->allCompletedShipments->completedShipments->shipments->shipmentNumber[0];
			}
		}
		return $DataparsedResponse;
	}

	function returnException($parsedResponse) {
		$return = array();
		$return['exceptionCode'] = $parsedResponse->Body->Fault->detail->exceptionDetails->exceptionCode;
		$return['exceptionText'] = $parsedResponse->Body->Fault->detail->exceptionDetails->exceptionText;
		return $return;
	}

	function returnWarnings($parsedResponse) {
		$return = array();
		if (isset($parsedResponse->Body->createShipmentResponse->integrationFooter->warnings->warning)) {
			foreach ($parsedResponse->Body->createShipmentResponse->integrationFooter->warnings->warning as $warning) {
				$content = array();
				$return[(string) $warning->warningCode[0]] = (string) $warning->warningDescription[0];
			}
		}
		return $return;
	}

	function returnLabel($parsedResponse) {
		$returnLabel = array();
		if (isset($parsedResponse->Body->printLabelResponse->label[0])) {
			$returnLabel = $parsedResponse->Body->printLabelResponse->label[0];
		}
		return $returnLabel;
	}

    function CancelShipment($transactionId, $shipmentNumber) {
    	try {
    		$time = date('c');
        	$response = false;
	        if (!empty($transactionId) && !empty($shipmentNumber)) {
	        	$request = array(
		            'integrationHeader' => array(
		                'dateTime' => $time,
		                'version' => '2',
		                'identification' => array(
		                    'applicationId' => $this->applicationId,
		                    'transactionId' => $transactionId
		                )
		            ),
		            'cancelShipments' => array('shipmentNumber' => $shipmentNumber),
		            'shipmentNumber' => $shipmentNumber,
	        	);

	        	$connectionDetails = $this->createRoyalMailSoap();
				$rmConnect = "";
				$xml_array = "";
				$requestedShipment = "";

				if (isset($connectionDetails['rmConnect'])) {
					$rmConnect = $connectionDetails['rmConnect'];
				}
		        $response = $rmConnect->cancelShipment($request);
		    }
        	return $response;
    	} catch (\Exception $e) {
			return $e->getMessage();
		}
    }

	function errorResponse($errorCode) {
	}
}

