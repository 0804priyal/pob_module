<?php

namespace Magenticity\RoyalMailShipping\Controller\Adminhtml\order;
use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;

class SubmitRoyalShipment extends \Magento\Backend\App\Action
{
    protected $shipmentLoader;
    protected $shipmentSender;
    private $shipmentValidator;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->shipmentSender = $shipmentSender;
        parent::__construct($context);
    }

    protected function _saveShipment($shipment) {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            \Magento\Framework\DB\Transaction::class
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();
        return $this;
    }

    public function execute() {
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('We can\'t save the shipment right now.'));
            $this->_redirect('adminhtml/order_shipment/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
        $data = $this->getRequest()->getParam('shipment');

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setCommentText($data['comment_text']);
        }
        try {
            $OrderId = $this->getRequest()->getParam('order_id');
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            $this->shipmentLoader->setOrderId($OrderId);
            $this->shipmentLoader->setShipmentId($shipmentId);
            $this->shipmentLoader->setShipment($data);
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                $this->_forward('noroute');
                return;
            }
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                $shipment->setCustomerNote($data['comment_text']);
                $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }
            $shipment->register();
            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $this->_saveShipment($shipment);
            $sendEmail = "";
            if (!empty($data['send_email'])) {
               $sendEmail = $data['send_email'];
            }

            if ($shipment->getId()) {
                $this->_redirect('sales/order/RoyalCreateShipment', ['shipment_id' => $shipment->getId(),'order_id' => $OrderId,'send_email' => $sendEmail,'shipment_detail' => '1']);
            } else {
                $this->messageManager->addError("Shipment was not generated.");
                $this->_redirect('adminhtml/order_shipment/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/order_shipment/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
    }
}
