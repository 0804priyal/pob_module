<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenticity\RoyalMailShipping\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\ModuleListInterface;

class Magenticity implements ObserverInterface
{
    protected $moduleList;

    public function __construct( ModuleListInterface $moduleList,
     \Magento\Store\Model\StoreManagerInterface $storeManager,
     \Magento\Framework\App\ProductMetadataInterface $productMetadata,
     \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress )
    {
        $this->_moduleList = $moduleList;
        $this->_storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
        $this->remoteAddress = $remoteAddress;
    }

	public function execute(\Magento\Framework\Event\Observer $observer) 
    {   
        $apiurl = 'https://www.magenticity.com';
        $modulename = 'Magenticity_RoyalMailShipping';
        $extension_type = 'COMMERCIAL';
        $servername = $_SERVER['SERVER_NAME'];
        $serveraddress = $_SERVER['SERVER_ADDR'];
        $serverhost = $_SERVER['HTTP_HOST'];

        $magento_version = $this->productMetadata->getVersion();
        $website =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $ip = $this->remoteAddress->getRemoteAddress();
        $extesion_version = $this->_moduleList->getOne($modulename)['setup_version'];
        $date = date('m/d/Y h:i:s a', time());
 
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL =>  $apiurl."/rest/all/V1/magenticity/getextensionrecord/".$servername."/".$serveraddress."/".$modulename,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

        } else {
            $elementid = trim($response, '"');
            /*Update Data Start*/

			if ($elementid != 'null') {
			/*Update Curl*/               
				$curlnew = curl_init();
				curl_setopt_array($curlnew, array(
    				CURLOPT_URL => $apiurl."/rest/all/V1/magenticity/extensionrecordupdate/".$elementid,
    				CURLOPT_RETURNTRANSFER => true,
    				CURLOPT_ENCODING => "",
    				CURLOPT_MAXREDIRS => 10,
    				CURLOPT_TIMEOUT => 30,
    				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    				CURLOPT_CUSTOMREQUEST => "PUT", 
    				CURLOPT_POSTFIELDS => "{\r\n\t\"data\":\r\n{\r\n \"track_id\" : \"$elementid\", \r\n \"updated_at\" : \"$date\",\r\n \"magento_version\"\t: \"$magento_version\",\r\n \"extension_version\" : \"$extesion_version\",\r\n \"extension_type\" : \"$extension_type\",\r\n \"server_host\" :\"$serverhost\",\r\n \"website\" : \"$website\",\r\n \"ip_addr\" : \"$ip\"\r\n},\r\n\"id\" :\"$elementid\"\r\n}",
    				CURLOPT_HTTPHEADER => array(
        				"Cache-Control: no-cache",
        				"Content-Type: application/json"
    				),
				));

				$response = curl_exec($curlnew);
				$err = curl_error($curlnew);
				curl_close($curlnew);
			} else {
	                $curlinsert = curl_init();
	                curl_setopt_array($curlinsert, array(
                        CURLOPT_URL =>  $apiurl."/rest/all/V1/magenticity/tracksave/",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\r\n\t\"data\": \r\n         {\r\n\t\"server_name\":\"$servername\",\r\n\t\"server_addr\":\"$serveraddress\",\r\n\t\"extension_name\":\"$modulename\",\r\n\t\"extension_version\": \"$extesion_version\",\r\n\t\"extension_type\" : \"$extension_type\",\r\n\t\"magento_version\" : \"$magento_version\",\r\n\t\"created_at\" : \"$date\",\r\n\t\"updated_at\" : \"$date\",\r\n\t\"server_host\" : \"$serverhost\",\r\n\t\"website\" : \"$website\",\r\n\t\"ip_addr\" : \"$ip\"\r\n\t\r\n}\r\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Cache-Control: no-cache",
                            "Content-Type: application/json"
                        ),
                    ));

                $response = curl_exec($curlinsert);
                $err = curl_error($curlinsert);
                curl_close($curlinsert);
            }
            /*Insert Data end*/
        }
    }
}
