<?php

namespace Magenticity\RoyalMailShipping\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


class ShipmentViewAction extends Column
{
    protected $urlBuilder;
    protected $royalMailCreateShip;
    protected $shipmentManager;
    protected $datahelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magenticity\RoyalMailShipping\Block\Adminhtml\RoyalMailCreateShip $_royalMailCreateShip,
        \Magento\Sales\Model\Order\Shipment $shipmentManager,
        \Magenticity\RoyalMailShipping\Helper\Data $datahelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->royalMailCreateShip = $_royalMailCreateShip;
        $this->shipmentManager = $shipmentManager;
        $this->datahelper = $datahelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $manualviewUrl = 'sales/order/RoyalShipPrintLabel';
        $CancelShipmentUrl = 'sales/order/RoyalShipCancelShipment';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $ShipmentEntityId = "";
                    $ShipmentEntityId = $item['entity_id'];
                    if (!empty($ShipmentEntityId)) {
                        $shipment = $this->shipmentManager->load($ShipmentEntityId);
                        if ($shipment) {
                            $TrackingNumber = $shipment->getRmTrackingNumber();
                            $OrderState = $shipment->getOrderState();
                            $CancelShipment = $shipment->getCancelRoyalShipment();
                            $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
                            if (empty($TrackingNumber)) {
                                $item[$this->getData('name')] = [
                                    'createroyalshipment' => [
                                        'href' => $this->urlBuilder->getUrl(
                                            $viewUrlPath,
                                            [
                                                $urlEntityParamName => $item['entity_id'],
                                                'order_id' => $shipment->getOrderId(),
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
                                                'order_id' => $shipment->getOrderId(),
                                                'shipmentlabel' => '1'
                                            ]
                                        ),
                                        'label' => __('PrintLabel')
                                    ]
                                ];
                            } elseif (!empty($TrackingNumber) && $CancelShipment == '1') {
                                $item[$this->getData('name')] = [
                                    'cancelled' => [
                                        'href' => '#',
                                        'label' => __('Canceled')
                                    ]
                                ];
                        } else {
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
                                        'label' => __('PrintLabel')
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
