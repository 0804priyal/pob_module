<?php

namespace StripeIntegration\Payments\Model\Method;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use StripeIntegration\Payments\Helper;
use StripeIntegration\Payments\Helper\Logger;

class Invoice extends \Magento\Payment\Model\Method\Adapter
{
    const METHOD_CODE = 'stripe_payments_invoice';
    protected $_code = self::METHOD_CODE;
    protected $type = 'invoice';
    protected $_formBlockType = 'StripeIntegration\Payments\Block\Method\Invoice';
    protected $_infoBlockType = 'StripeIntegration\Payments\Block\PaymentInfo\Invoice';

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Payment\Gateway\Config\ValueHandlerPoolInterface $valueHandlerPool,
        \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory,
        $code,
        $formBlockType,
        $infoBlockType,
        \StripeIntegration\Payments\Model\Config $config,
        \StripeIntegration\Payments\Helper\Generic $helper,
        \StripeIntegration\Payments\Model\Stripe\ProductFactory $productFactory,
        \StripeIntegration\Payments\Model\Stripe\PriceFactory $priceFactory,
        \StripeIntegration\Payments\Model\Stripe\CouponFactory $couponFactory,
        \StripeIntegration\Payments\Model\Stripe\InvoiceItemFactory $invoiceItemFactory,
        \StripeIntegration\Payments\Model\Stripe\InvoiceFactory $invoiceFactory,
        \StripeIntegration\Payments\Model\InvoiceFactory $orderInvoiceFactory,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool = null,
        \Magento\Payment\Gateway\Validator\ValidatorPoolInterface $validatorPool = null
    ) {
        $this->config = $config;
        $this->helper = $helper;
        $this->customer = $helper->getCustomerModel();
        $this->productFactory = $productFactory;
        $this->priceFactory = $priceFactory;
        $this->couponFactory = $couponFactory;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->orderInvoiceFactory = $orderInvoiceFactory;
        $this->cache = $cache;

        parent::__construct(
            $eventManager,
            $valueHandlerPool,
            $paymentDataObjectFactory,
            $code,
            $formBlockType,
            $infoBlockType,
            $commandPool,
            $validatorPool
        );
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (!$this->config->isEnabled())
            return false;

        return parent::isAvailable($quote);
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        $daysDue = $data->getAdditionalData('days_due');
        $daysDue = max(0, $daysDue);
        $daysDue = min(999, $daysDue);
        $info = $this->getInfoInstance();
        $info->setAdditionalInformation('days_due', $daysDue);

        if ($this->config->getIsStripeAPIKeyError())
            $this->helper->dieWithError("Invalid API key provided");

        return $this;
    }

    public function capture(InfoInterface $payment, $amount)
    {
        if ($amount > 0)
        {
            if ($payment->getAdditionalInformation('invoice_id'))
                throw new LocalizedException(__("This order cannot be captured from Magento. The invoice will be automatically updated once the customer has paid through a Stripe hosted invoice page."));

            $info = $this->getInfoInstance();
            $order = $info->getOrder();
            $this->customer->createStripeCustomerIfNotExists(false, $order);
            $customerId = $this->customer->getStripeId();

            $this->customer->updateFromOrder($order);
            $invoice = $this->createInvoice($order, $customerId)->finalize();
            $payment->setAdditionalInformation('invoice_id', $invoice->getId());
            $payment->setLastTransId($invoice->getStripeObject()->payment_intent);
            $payment->setIsTransactionPending(true);
            $order->setCanSendNewEmailFlag(true);
            $this->config->getStripeClient()->invoices->sendInvoice($invoice->getId(), []);
        }

        return $this;
    }

    public function createInvoice($order, $customerId)
    {
        $currencyCode = $order->getOrderCurrencyCode();
        $items = $order->getAllItems();

        if (empty($items))
            throw new \Exception("Could not create Stripe invoice because the order contains no items.");

        foreach ($items as $item)
        {
            if (!in_array($item->getProductType(), ["simple", "virtual", "downloadable"]))
                continue;

            $this->productFactory->create()->fromOrderItem($item);
            $this->invoiceItemFactory->create()->fromOrderItem($item, $order, $customerId);
        }

        $coupon = $this->couponFactory->create()->fromOrder($order);
        $this->invoiceItemFactory->create()->fromShipping($order, $customerId);
        $this->invoiceItemFactory->create()->fromTax($order, $customerId);

        $invoice = $this->invoiceFactory->create()->fromOrderItem($item, $order, $customerId, $coupon->getStripeObject());
        if ($invoice->getId())
        {
            $this->orderInvoiceFactory->create()
                ->setInvoiceId($invoice->getId())
                ->setOrderIncrementId($order->getIncrementId())
                ->save();
        }

        return $invoice;
    }

    public function refund(InfoInterface $payment, $amount)
    {
        $creditmemo = $payment->getCreditmemo();
        if (!empty($creditmemo))
        {
            $rate = $creditmemo->getBaseToOrderRate();
            if (!empty($rate) && is_numeric($rate) && $rate > 0)
            {
                $amount = round($amount * $rate, 2);
                $diff = $amount - $payment->getAmountPaid();
                if ($diff > 0 && $diff <= 1) // Solves a currency conversion rounding issue (Magento rounds .5 down)
                    $amount = $payment->getAmountPaid();
            }
        }

        $currency = $payment->getOrder()->getOrderCurrencyCode();

        $transactionId = $payment->getLastTransId();

        // Case where an invoice is in Pending status, with no transaction ID, receiving a source.failed event which cancels the invoice.
        if (empty($transactionId))
            return $this;

        $transactionId = preg_replace('/-.*$/', '', $transactionId);

        try {
            $cents = 100;
            if ($this->helper->isZeroDecimal($currency))
                $cents = 1;

            $params = array();
            if ($amount > 0)
                $params["amount"] = round($amount * $cents);

            $pi = \Stripe\PaymentIntent::retrieve($transactionId);
            $charge = $pi->charges->data[0];

            $params["charge"] = $charge->id;

            // This is true when an authorization has expired or when there was a refund through the Stripe account
            if (!$charge->refunded)
            {
                $this->cache->save($value = "1", $key = "admin_refunded_" . $charge->id, ["stripe_payments"], $lifetime = 60 * 60);
                $refund = $this->config->getStripeClient()->refunds->create(['charge' => $charge->id]);
                $payment->setAdditionalInformation('last_refund_id', $refund->id);
            }
            else
            {
                $comment = __('An attempt to manually refund the order was made, however this order was already refunded in Stripe. Creating an offline refund instead.');
                $payment->getOrder()->addStatusToHistory($status = false, $comment, $isCustomerNotified = false);
            }
        }
        catch (\Exception $e)
        {
            $this->helper->dieWithError('Could not refund payment: '.$e->getMessage());
            throw new \Exception(__($e->getMessage()));
        }

        return $this;
    }

    // Disables the Capture button on the invoice page
    public function canCapture()
    {
        $info = $this->getInfoInstance();
        if ($info->getAdditionalInformation('invoice_id'))
            return false;

        return true;
    }
}
