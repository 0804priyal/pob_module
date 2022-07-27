<?php
namespace Magenticity\RoyalMailShipping\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    private $_ServiceOfferFactory;
    private $_ServiceEnhancementFactory;
    private $_ServiceContainerFactory;
    private $_ServiceIntegrationFactory;
    private $_ServiceTypeFactory;
    private $_SignatureFactory;

    public function __construct(
        \Magenticity\RoyalMailShipping\Model\ServiceOfferFactory $ServiceOfferFactory,
        \Magenticity\RoyalMailShipping\Model\ServiceEnhancementFactory $ServiceEnhancementFactory,
        \Magenticity\RoyalMailShipping\Model\ServiceContainerFactory $ServiceContainerFactory,
        \Magenticity\RoyalMailShipping\Model\ServiceIntegrationFactory $ServiceIntegrationFactory,
        \Magenticity\RoyalMailShipping\Model\ServiceTypeFactory $ServiceTypeFactory,
        \Magenticity\RoyalMailShipping\Model\SignatureFactory $SignatureFactory,
        \Magento\Framework\Filesystem\DirectoryList $DirectoryList,
        \Magento\Framework\File\Csv $fileCsv
    ) {
        $this->_ServiceOfferFactory = $ServiceOfferFactory;
        $this->_ServiceEnhancementFactory = $ServiceEnhancementFactory;
        $this->_ServiceContainerFactory = $ServiceContainerFactory;
        $this->_ServiceIntegrationFactory = $ServiceIntegrationFactory;
        $this->_ServiceTypeFactory = $ServiceTypeFactory;
        $this->_SignatureFactory = $SignatureFactory;
        $this->_DirectoryList = $DirectoryList;
        $this->_fileCsv = $fileCsv;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $RoyalMailShippingApp = $this->_DirectoryList->getPath("app");
        $RoyalMailShippingDirectory = $RoyalMailShippingApp."/code/Magenticity/RoyalMailShipping/_fixtures/";
        $RoyalMailShippingFile = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping.csv';
        $RoyalMailShippingEnhancementFile = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping_serviceenhancement.csv';
        $RoyalMailShippingFormatFile = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping_serviceformat.csv';
        $RoyalMailShippingServiceIntegraFile = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping_serviceintegration.csv';
        $RoyalMailShippingTypeFile = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping_servicetype.csv';
        $RoyalMailShippingSignature = $RoyalMailShippingDirectory . '/magenticity_royalmailshipping_signature.csv';

        $serviceOfferDetail = array();
        if (file_exists($RoyalMailShippingFile)) {
            $RoyalMailShippingData = $this->_fileCsv->getData($RoyalMailShippingFile);
            if (!empty($RoyalMailShippingData)) {
                foreach ($RoyalMailShippingData as $key => $serviceoffervalue) {
                    $RoyalServiceOfferFactory = $this->_ServiceOfferFactory->create();
                    $serviceOfferDetail = ['service_offering_code' => $serviceoffervalue[1],'service_offering_name' => $serviceoffervalue[2]];
                    $RoyalServiceOfferFactory->setData($serviceOfferDetail);
                    $RoyalServiceOfferFactory->save();
                }
            }
        }

        $serviceEnhanceDetail = array();
        if (file_exists($RoyalMailShippingEnhancementFile)) {
            $RoyalMailShippingEnhancementData = $this->_fileCsv->getData($RoyalMailShippingEnhancementFile);
            if (!empty($RoyalMailShippingEnhancementData)) {
                foreach ($RoyalMailShippingEnhancementData as $key => $serviceenhancevalue) {
                    $RoyalServiceEnhanceFactory = $this->_ServiceEnhancementFactory->create();
                    $serviceEnhanceDetail = ['enhancement_type' => $serviceenhancevalue[1],'enhancement_desc' => $serviceenhancevalue[2],'enhancement_group' => $serviceenhancevalue[3]];
                    $RoyalServiceEnhanceFactory->setData($serviceEnhanceDetail);
                    $RoyalServiceEnhanceFactory->save();
                }
            }
        }

        $ServiceFormatDetail = array();
        if (file_exists($RoyalMailShippingFormatFile)) {
            $RoyalMailShippingFormatData = $this->_fileCsv->getData($RoyalMailShippingFormatFile);
            if (!empty($RoyalMailShippingFormatData)) {
                foreach ($RoyalMailShippingFormatData as $key => $serviceformatvalue) {
                    $RoyalServiceContainerFactory = $this->_ServiceContainerFactory->create();
                    $ServiceFormatDetail = ['service_format_code' => $serviceformatvalue[1],'service_format_desc' => $serviceformatvalue[2]];
                    $RoyalServiceContainerFactory->setData($ServiceFormatDetail);
                    $RoyalServiceContainerFactory->save();
                }
            }
        }

        $ServiceIntegrationDetail = array();
        if (file_exists($RoyalMailShippingServiceIntegraFile)) {
            $RoyalMailShippingIntegrData = $this->_fileCsv->getData($RoyalMailShippingServiceIntegraFile);
            if (!empty($RoyalMailShippingIntegrData)) {
                foreach ($RoyalMailShippingIntegrData as $key => $ServiceInteValue) {
                    $RoyalServiceIntegrationFactory = $this->_ServiceIntegrationFactory->create();
                    $ServiceIntegrationDetail = ['service_type' => $ServiceInteValue[1],'service_offering' => $ServiceInteValue[2],'service_format' => $ServiceInteValue[3],'enhancement_type' => $ServiceInteValue[4],'signature' => $ServiceInteValue[5]];
                    $RoyalServiceIntegrationFactory->setData($ServiceIntegrationDetail);
                    $RoyalServiceIntegrationFactory->save();
                }
            }
        }

        $ServiceTypeDetail = array();
        if (file_exists($RoyalMailShippingTypeFile)) {
            $RoyalMailShippingTypeData = $this->_fileCsv->getData($RoyalMailShippingTypeFile);
            if (!empty($RoyalMailShippingTypeData)) {
                foreach ($RoyalMailShippingTypeData as $key => $ServiceTypeValue) {
                    $RoyalShippingTypeFactory = $this->_ServiceTypeFactory->create();
                    $ServiceTypeDetail = ['service_type' => $ServiceTypeValue[1],'service_desc' => $ServiceTypeValue[2]];
                    $RoyalShippingTypeFactory->setData($ServiceTypeDetail);
                    $RoyalShippingTypeFactory->save();
                }
            }
        }

        $SignatureDetail = array();
        if (file_exists($RoyalMailShippingSignature)) {
            $RoyalMailShippingSignatureData = $this->_fileCsv->getData($RoyalMailShippingSignature);
            if (!empty($RoyalMailShippingSignatureData)) {
                foreach ($RoyalMailShippingSignatureData as $key => $SignatureValue) {
                    $RoyalSignatureFactory = $this->_SignatureFactory->create();
                    $SignatureDetail = ['signature_code' => $SignatureValue[1],'signature_desc' => $SignatureValue[2]];
                    $RoyalSignatureFactory->setData($SignatureDetail);
                    $RoyalSignatureFactory->save();
                }
            }
        }
        $installer->endSetup();
    }
}