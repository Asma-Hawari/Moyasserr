<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Controller\Onepage;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Sales\Model\Order;

/**
 * Onepage checkout success controller class
 */
class Success extends \Magento\Checkout\Controller\Onepage implements HttpGetActionInterface
{
    /**
     * @var mixed|string
     */
    const ORDER_STATUS = Order::STATE_PROCESSING;

    /**
     * @var mixed|string
     */
    const CHECK_STATUS_URL = 'https://einvoicing.mobicashpayments.com/checkout/GetBillStatus';

    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $session->clearQuote();
        //@todo: Refactor it to match CQRS
        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch(
            'checkout_onepage_controller_success_action',
            [
                'order_ids' => [$session->getLastOrderId()],
                'order' => $session->getLastRealOrder()
            ]
        );
        //if (!empty($this->_request->getParam('transid'))) {
        if ($session->getLastRealOrder()) {
            $order = $session->getLastRealOrder();
            $orderId = $order->getIncrementId();
            $result = $this->checkStatus();
            if ($result->result == 'payed') {
                $order = $session->getLastRealOrder();
                $orderState = self::ORDER_STATUS;
                $order->setStatus($orderState);
                $order->save();
                $order->setState($orderState, true)->save();
            }
        }
        //}
        return $resultPage;
    }

    public function checkStatus()
    {
        $data = array(
            "login" => "ARBK-41-ABTESTEINV",
            "pwd_hash_block" => "B21E60A158D92511DB188AF8D9C82ACE3EF9495E76989A0B53035DCEDD61FC5A",
            "salt" => "abc123@",
            "Id" => 2774843,
            "PaymentRid" => 23656
        );
        $response = $this->curl_post(self::CHECK_STATUS_URL, $data);
        $decodeJson = json_decode($response , true);
        $createBill= $decodeJson["GetBillStatus"]["_attributes"];
        $status = $createBill["Status"];
        return $status;
    }

    function curl_post($url, array $post = NULL, array $options = array())
    {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($post)
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        return $result;

    }

    public function getToken()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/maktapp/merchant_key');
        return $conf;
    }
}

