<?php

namespace Magenticity\RoyalMailShipping\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


class ViewAction extends Column
{
    protected $urlBuilder;
    protected $royalMailCreateShip;
    protected $datahelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magenticity\RoyalMailShipping\Block\Adminhtml\RoyalMailCreateShip $_royalMailCreateShip,
        \Magenticity\RoyalMailShipping\Helper\Data $datahelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->royalMailCreateShip = $_royalMailCreateShip;
        $this->datahelper = $datahelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $manualviewUrl = 'sales/order/RoyalPrintLabel';
        $CancelShipmentUrl = 'sales/order/CancelShipment';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
                    $TrackingNumber = "";
                    $IsCancelShipment = "";
                    $OrderState = "";
                    $TotalShipment = "";
                    $canShip = "";
                    $RoyalTrackingNumber = $this->royalMailCreateShip->getRoyalmailTrackingNumber($item['entity_id']);
                    if (isset($RoyalTrackingNumber['royal_tracking_number'])) {
                        $TrackingNumber = $RoyalTrackingNumber['royal_tracking_number'];
                    }
                    if (isset($RoyalTrackingNumber['cancel_shipment'])) {
                        $IsCancelShipment = $RoyalTrackingNumber['cancel_shipment'];
                    }
                    if (isset($RoyalTrackingNumber['order_state'])) {
                        $OrderState = $RoyalTrackingNumber['order_state'];
                    }
                    if (isset($RoyalTrackingNumber['shipment_count'])) {
                        $TotalShipment = $RoyalTrackingNumber['shipment_count'];
                    }
                    if (isset($RoyalTrackingNumber['canship'])) {
                        $canShip = $RoyalTrackingNumber['canship'];
                    }

                    if ($canShip == "1") {
                        $ShipmentDetailUrl = $this->urlBuilder->getUrl("sales/order/view/order_id/{$item['entity_id']}/active_tab/order_shipments");
                        $item[$this->getData('name')] = [
                            'manageship' => [
                                'href' => $ShipmentDetailUrl,
                                'label' => __('Manage Shipment')
                            ]
                        ];
                    } elseif ($TotalShipment > 1) {
                        $ShipmentDetailUrl = $this->urlBuilder->getUrl("sales/order/view/order_id/{$item['entity_id']}/active_tab/order_shipments");
                        $item[$this->getData('name')] = [
                            'manageship' => [
                                'href' => $ShipmentDetailUrl,
                                'label' => __('Manage Shipment')
                            ]
                        ];
                    } else {
                        if ($OrderState != "canceled") {
                            if (!empty($TrackingNumber) && $IsCancelShipment != '1') {
                                $item[$this->getData('name')] = [
                                    'createroyalshipment' => [
                                        'href' => '#',
                                        'label' => __('Shipped')
                                    ],
                                    'printroyallabel' => [
                                        'href' => $this->urlBuilder->getUrl(
                                            $manualviewUrl,
                                            [
                                                $urlEntityParamName => $item['entity_id']
                                            ]
                                        ),
                                        'label' => __('Print Label')
                                    ],
                                    'cancel' => [
                                        'href' => $this->urlBuilder->getUrl(
                                            $CancelShipmentUrl,
                                            [
                                                $urlEntityParamName => $item['entity_id']
                                            ]
                                        ),
                                        'label' => __('Cancel Shipment')
                                    ]
                                ];
                            } elseif (!empty($TrackingNumber) && $IsCancelShipment == '1') {
                                $item[$this->getData('name')] = [
                                    'cancelled' => [
                                        'href' => '#',
                                        'label' => __('Canceled')
                                    ]
                                ];
                            } else {
                                $item[$this->getData('name')] = [
                                    'createroyalshipment' => [
                                        'href' => $this->urlBuilder->getUrl(
                                            $viewUrlPath,
                                            [
                                                $urlEntityParamName => $item['entity_id'],
                                                'createshipment' => '1'
                                            ]
                                        ),
                                        'label' => __('Create Shipment')
                                    ],
                                    'printroyallabel' => [
                                        'href' => $this->urlBuilder->getUrl(
                                            $viewUrlPath,
                                            [
                                                $urlEntityParamName => $item['entity_id'],
                                                'shipmentlabel' => '1'
                                            ]
                                        ),
                                        'label' => __('Print Label')
                                    ]
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $dataSource;
    }

    public function prepare() {
        parent::prepare();
        $IsEnable = $this->datahelper->IsModuleEnable();
        if ($IsEnable) {
            $this->_data['config']['componentDisabled'] = false;
        } else {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
