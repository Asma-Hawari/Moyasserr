<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Controller\Standard;
/**
 * Class Redirect
 *
 * @package Moyasser\STC\Controller\Standard
 */
class Redirect extends \Moyasser\STC\Controller\STCPay
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $order = $this->getRealOrderId();
        if ($order->getBillingAddress()) {
            $this->addOrderHistory($order,'<br/>The customer was redirected to STCPay Payment Page');
            $redirectLink = $this->stcPayModel->buildMyasserPayRequest($order);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($redirectLink);
            return $resultRedirect;
        } else {
            $this->cancelPayment();
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setUrl('checkout');
        }
    }
}