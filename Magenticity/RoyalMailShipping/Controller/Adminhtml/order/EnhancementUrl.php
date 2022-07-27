<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class EnhancementUrl extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $ServiceIntegrationCollection;
    protected $ServiceEnhancementCollection;
    protected $RoyalMailCreateShip;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceEnhancement\CollectionFactory $ServiceEnhancementCollection,
        \Magenticity\RoyalMailShipping\Block\Adminhtml\RoyalMailCreateShip $RoyalMailCreateShip
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ServiceIntegrationCollection = $ServiceIntegrationCollection;
        $this->ServiceEnhancementCollection = $ServiceEnhancementCollection;
        $this->RoyalMailCreateShip = $RoyalMailCreateShip;
    }

    public function execute() {
        $ServiceType = "";
        $ServiceOffering = "";
        $ServiceFormat = "";
        $ServiceEnhancementData = "";
        $GetEnhancementValue = array();
        $serviceEnhancementDetails = array();
        $GetServiceTypeParam = "";
        $GetServiceOfferingParam = "";
        $GetServiceFormatParam = "";

        $GetServiceTypeParam = $this->getRequest()->getParam('getServiceType');
        if (!empty($GetServiceTypeParam)) {
            $ServiceType = $GetServiceTypeParam;
        }
        $GetServiceOfferingParam = $this->getRequest()->getParam('getServiceOffering');
        if (!empty($GetServiceOfferingParam)) {
            $ServiceOffering = $GetServiceOfferingParam;
        }
        $GetServiceFormatParam = $this->getRequest()->getParam('getServiceFormat');
        if (!empty($GetServiceFormatParam)) {
            $ServiceFormat = $GetServiceFormatParam;
        }

        if (!empty($ServiceType) && !empty($ServiceOffering) && !empty($ServiceFormat)) {
            $ServiceEnhancement = $this->ServiceIntegrationCollection->create()->addFieldToSelect('enhancement_type')->addFieldToFilter('service_type', $ServiceType)->addFieldToFilter('service_offering', $ServiceOffering)->addFieldToFilter('service_format', $ServiceFormat);
            $ServiceEnhancementData = $ServiceEnhancement->getData();
        }

        $ConfigServiceEnhancement = $this->RoyalMailCreateShip->getServiceEnhancement();

        if (!empty($ServiceEnhancementData)) {
            foreach ($ServiceEnhancementData as $key => $EnhancementType) {
                $EnhancementValue = $EnhancementType['enhancement_type'];
                if (!empty($EnhancementValue) && $EnhancementValue != "null") {
                    $GetEnhancementValue[] = $EnhancementValue;
                }
            }
        }
        $UniqEnhancementValue = array_unique($GetEnhancementValue);
        $CompareEnhancementValue = array_intersect($UniqEnhancementValue,$ConfigServiceEnhancement);
        if (!empty($CompareEnhancementValue)) {
            foreach ($CompareEnhancementValue as $key => $serviceenhancementtype) {
                $GetServiceEnhanceDesc = $this->ServiceEnhancementCollection->create()->addFieldToSelect('*')->addFieldToFilter('enhancement_type', $serviceenhancementtype);
                if (!empty($GetServiceEnhanceDesc->getFirstItem()->getData())) {
                    $serviceEnhancementDetails[] = $GetServiceEnhanceDesc->getFirstItem()->getData();
                }
            }
        }
        $result = $this->resultJsonFactory->create();
        $result->setData($serviceEnhancementDetails);
        return $result;
    }
}
