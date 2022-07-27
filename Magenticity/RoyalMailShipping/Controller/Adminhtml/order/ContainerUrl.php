<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class ContainerUrl extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $ServiceIntegrationCollection;
    protected $ServiceContainerCollection;
    protected $RoyalMailCreateShip;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceContainer\CollectionFactory $ServiceContainerCollection,
        \Magenticity\RoyalMailShipping\Block\Adminhtml\RoyalMailCreateShip $RoyalMailCreateShip
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ServiceIntegrationCollection = $ServiceIntegrationCollection;
        $this->ServiceContainerCollection = $ServiceContainerCollection;
        $this->RoyalMailCreateShip = $RoyalMailCreateShip;
    }

    public function execute() {
        $ServiceContainerData = "";
        $ServiceOfferType = "";
        $ServiceOfferTypeParam = "";
        $ServiceOfferingParam = $this->getRequest()->getParam('serviceoffering');
        if (!empty($ServiceOfferingParam)) {
            $serviceOffering = $ServiceOfferingParam;
            $ServiceContainer = $this->ServiceIntegrationCollection->create()->addFieldToSelect('service_format')->addFieldToFilter('service_offering', $serviceOffering);
            $ServiceContainerData = $ServiceContainer->getData();
        }
        $ServiceOfferTypeParam = $this->getRequest()->getParam('ServiceOfferType');
        if (!empty($ServiceOfferTypeParam)) {
            $ServiceOfferType = $ServiceOfferTypeParam;
        }

        $ServiceContainerDetails = array();
        $GetServiceContainer = array();
        $CompareServiceFormat = "";
        $serviceFormatDetails = array();

        if (!empty($ServiceContainerData)) {
            foreach ($ServiceContainerData as $key => $ServiceContainerValue) {
                if ($ServiceOfferType == "I" && $ServiceContainerValue['service_format'] == "N") {
                    $ServiceContainerValue['service_format'] = "I_N";
                }
                if ($ServiceOfferType == "I" && $ServiceContainerValue['service_format'] == "P") {
                    $ServiceContainerValue['service_format'] = "I_P";
                }
                $GetServiceContainer[] = $ServiceContainerValue['service_format'];
            }
        }

        if (!empty($GetServiceContainer)) {
            $ServiceContainerDetails = array_unique($GetServiceContainer);
            $ConfigServiceFormat = $this->RoyalMailCreateShip->getServiceFormat();
            $CompareServiceFormat = array_intersect($ServiceContainerDetails,$ConfigServiceFormat);
        }

        if (!empty($CompareServiceFormat)) {
            foreach ($CompareServiceFormat as $key => $serviceformat) {
                $GetServiceFormatDesc = $this->ServiceContainerCollection->create()->addFieldToSelect('*')->addFieldToFilter('service_format_code', $serviceformat);
                if (!empty($GetServiceFormatDesc->getFirstItem()->getData())) {
                    $serviceFormatDetails[] = $GetServiceFormatDesc->getFirstItem()->getData();
                }
            }
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($serviceFormatDetails);
        return $result;
    }
}
