<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Controller\Standard;

/**
 * Class Cancel
 * Cancel Class is called whenever a payment has been canceled
 *
 * @package Moyasser\STC\Controller\Standard
 */
class Cancel extends \Moyasser\STC\Controller\STCPay
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $this->_cancelPayment();
        $this->_checkoutSession->restoreQuote();
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setUrl('checkout');
    }
}
