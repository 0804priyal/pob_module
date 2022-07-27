<?php

namespace Magenticity\RoyalMailShipping\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $storeManager;
    protected $_encryptor;
    protected $_customerSession;
    protected $resource;
    protected $ServiceIntegrationCollection;
    protected $ServiceTypeCollection;

	public function __construct(
    	\Magento\Framework\App\Helper\Context $context,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\Session $_customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceType\CollectionFactory $ServiceTypeCollection
    )
    {
    	$this->storeManager = $storeManager;
        $this->_encryptor = $encryptor;
        $this->_customerSession = $_customerSession;
        $this->resource = $resource;
        $this->ServiceIntegrationCollection = $ServiceIntegrationCollection;
        $this->ServiceTypeCollection = $ServiceTypeCollection;
    	parent::__construct($context);
    }
    public function getConfig($path = null, $scopeType = ScopeInterface::SCOPE_STORE, $store = null) {
        if ($store === null) {
            $store = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue($path, $scopeType, $store);
    }

    public function serviceType() {
        $serviceOfferConfig = $this->getServiceOfferConfig();
        $ConvertServiceOfferConfig = explode(",", $serviceOfferConfig);
        $ServiceTypeData = array();
        $offeringdetails = array();
        $getfinalvalue = array();
        if (!empty($ConvertServiceOfferConfig)) {
            foreach ($ConvertServiceOfferConfig as $key => $ConvertServiceOfferValue) {
                $ServiceType = $this->ServiceIntegrationCollection->create()->addFieldToSelect('service_type')->addFieldToFilter('service_offering', $ConvertServiceOfferValue);
                $ServiceTypeData[] = $ServiceType->getData();
            }
        }
        if (!empty($ServiceTypeData)) {
            foreach ($ServiceTypeData as $key => $ServiceTypeValue) {
                foreach ($ServiceTypeValue as $key => $getvalue) {
                    $getfinalvalue[] = $getvalue['service_type'];
                }
            }
            $GetServiceTypeValue = array_unique($getfinalvalue);
            foreach ($GetServiceTypeValue as $key => $value) {
               $GetServiceTypeCollection = $this->ServiceTypeCollection->create()->addFieldToSelect('*')->addFieldToFilter('service_type', $value);
                $offeringdetails[] = $GetServiceTypeCollection->getFirstItem()->getData();
            }
        }
        return $offeringdetails;
    }

    public function getServiceOfferConfig() {
        return $this->getConfig('royalmailshipping/trackingapi/service_offering');
    }

    public function getServiceFormatConfig() {
        return $this->getConfig('royalmailshipping/trackingapi/allowed_container');
    }

    public function getApiUserName() {
        return $this->getConfig('royalmailshipping/trackingapi/api_username');
    }

    public function getApiPassword() {
        $apiPass = $this->getConfig('royalmailshipping/trackingapi/api_password');
        $DecryptapiPass = $this->_encryptor->decrypt($apiPass);
        return $DecryptapiPass;
    }

    public function getApiApplicationId() {
        return $this->getConfig('royalmailshipping/trackingapi/api_applicationId');
    }

    public function getApiClientId() {
        return $this->getConfig('royalmailshipping/trackingapi/api_clientId');
    }

    public function getApiClientSecret() {
        $apiClientSecret = $this->getConfig('royalmailshipping/trackingapi/api_clientSecret');
        $DecryptapiClientSecret = $this->_encryptor->decrypt($apiClientSecret);
        return $DecryptapiClientSecret;
    }

    public function IsServiceEnhancement() {
        return $this->getConfig('royalmailshipping/trackingapi/service_enhancement');
    }

    public function ServiceEnhancementType() {
        return $this->getConfig('royalmailshipping/trackingapi/service_enhancement_type');
    }

    public function IsSignatureOffering() {
        return $this->getConfig('royalmailshipping/trackingapi/signature_required');
    }

    public function IsModuleEnable() {
        return $this->getConfig('royalmailshipping/trackingapi/enable');
    }

    public function getWeightUnit() {
        return $this->getConfig('general/locale/weight_unit');
    }

    public function SignatureOffering() {
        if ($this->IsSignatureOffering()) {
            return $this->getConfig('royalmailshipping/trackingapi/signature_required_offering');
        } else {
            return false;
        }
    }
}