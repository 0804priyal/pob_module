<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;

class ServiceOfferingUrl extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $ServiceIntegrationCollection;
    protected $ServiceOfferCollection;
    protected $RoyalMailCreateShip;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceIntegration\CollectionFactory $ServiceIntegrationCollection,
        \Magenticity\RoyalMailShipping\Model\ResourceModel\ServiceOffer\CollectionFactory $ServiceOfferCollection,
        \Magenticity\RoyalMailShipping\Block\Adminhtml\RoyalMailCreateShip $RoyalMailCreateShip
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ServiceIntegrationCollection = $ServiceIntegrationCollection;
        $this->ServiceOfferCollection = $ServiceOfferCollection;
        $this->RoyalMailCreateShip = $RoyalMailCreateShip;
    }

    public function execute() {
        $ServiceOfferingData = "";
        $ServiceTypeParam = "";
        $ServiceTypeParam = $this->getRequest()->getParam('servicetype');
        if (!empty($ServiceTypeParam)) {
            $ServiceType = $ServiceTypeParam;
            $ServiceOffering = $this->ServiceIntegrationCollection->create()->addFieldToSelect('service_offering')->addFieldToFilter('service_type', $ServiceType);
            $ServiceOfferingData = $ServiceOffering->getData();
        }

        $GetServiceOffering = array();
        $GetServiceOfferingDetails = "";
        $GetServiceOfferingDesc = array();
        $serviceOfferingDetails = array();
        $CompareServiceOffering = array();

        if (!empty($ServiceOfferingData)) {
            foreach ($ServiceOfferingData as $key => $ServiceOfferingValue) {
                $GetServiceOffering[] = $ServiceOfferingValue['service_offering'];
            }
        }

        if (!empty($GetServiceOffering)) {
            $GetServiceOfferingDetails = array_unique($GetServiceOffering);
            $ConfigServiceOffering = $this->RoyalMailCreateShip->getServiceOffering();
            $CompareServiceOffering = array_intersect($GetServiceOfferingDetails,$ConfigServiceOffering);

            if (!empty($CompareServiceOffering)) {
                foreach ($CompareServiceOffering as $key => $servicevalue) {
                    $GetServiceOfferingDesc = $this->ServiceOfferCollection->create()->addFieldToSelect('*')->addFieldToFilter('service_offering_code', $servicevalue);
                    if (!empty($GetServiceOfferingDesc->getFirstItem()->getData())) {
                        $serviceOfferingDetails[] = $GetServiceOfferingDesc->getFirstItem()->getData();
                    }
                }
            }
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($serviceOfferingDetails);
        return $result;
    }
}
