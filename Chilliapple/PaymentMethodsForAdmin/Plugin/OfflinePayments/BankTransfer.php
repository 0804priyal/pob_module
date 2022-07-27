<?php

namespace Chilliapple\PaymentMethodsForAdmin\Plugin\OfflinePayments;

use Magento\Backend\Model\Auth\Session as BackendSession;
/**
 * Class BankTransfer
 */
class BankTransfer
{
    protected $backendSession;

    public function __construct(
    BackendSession $backendSession
    ){
        $this->backendSession = $backendSession;
    }
    /**
     * Execute after the IsAvailable function
     * @return bool
     */
    public function afterIsAvailable($subject, $result)
    {
        if($this->backendSession->isLoggedIn())
        {
            return $result;
        }
        else
        {
            false;
        }

    }
}
