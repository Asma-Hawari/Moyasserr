<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Model;

use Moyasser\STC\Helper\Data as DataHelper;
use Moyasser\STC\Controller\Standard;

/**
 * Class STCPay
 *
 * @package Moyasser\STC\Model
 */
class STCPay extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * @var mixed|string
     */
    const CODE = 'mysStc';

    /**
     * @var mixed|string
     */
    protected $code = self::CODE;

    /**
     * @var bool
     */
    protected $isGateway = true;

    /**
     * @var bool
     */
    protected $isOffline = true;

    /**
     * @var bool
     */
    protected $canRefund = true;

    /**
     * @var bool
     */
    protected $canCapture = true;

    /**
     * @var bool
     */
    protected $canAuthorize = true;

    /**
     * @var bool
     */
    protected $canRefundInvoicePartial = true;

    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var string|null
     */
    protected $minAmount = null;

    /**
     * @var string|null
     */
    protected $maxAmount = null;

    /**
     * @var array
     */
    protected $supportedCurrencyCodes = array('INR');

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * STCPay constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param DataHelper $helper
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Moyasser\STC\Helper\Data $helper,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Framework\App\Request\Http $request
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

        $this->minAmount = "0.100";
        $this->maxAmount = "1000000";
        $this->urlBuilder = $urlBuilder;
        $this->order = $order;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->invoice = $invoice;
        $this->request = $request;
        $this->helper = $helper;
    }

    public function getCode()
    {
        if (empty($this->code)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot retrieve the payment method code.')
            );
        }
        return $this->code;
    }

    /**
     * @param  $order
     *
     * @return string
     */
    /*public function buildMyasserPayRequest($order)
    {
        $customerPhone = $order->getBillingAddress()->getTelephone();
        $data = array(
            "login" => $this->helper->getLogin(),
            "description" => "",
            "order_id" => $order->getRealOrderId(),
            "amount" => round($order->getGrandTotal(), 3),
            "tax_amount" => "0",
            "currency_alpha_code" => "JOD",
            "success_url" => $this->getSuccessURL(),
            "expired_url" => $this->getFailureURL(),
            "cancel_url" => $this->getFailureURL(),
            "return_url" => $this->getFailureURL()
        );

        $orderItems = $order->getAllItems();
        $i = 1;
        $items = [];
        foreach ($orderItems as $item) {
            $items += ["item_code_" . $i => $item->getSku()];
            $items += ["item_description_" . $i => $item->getDescription()];
            $items += ["item_quantity_" . $i => $item->getQtyOrdered()];
            $items += ["item_amount_" . $i => $item->getBaseCost()];
            $items += ["item_unit_" . $i => "шт"];
            $i++;
        }
        $jsonData = array_merge($data, $items);

        $result = $this->curlPost('https', $jsonData);

        return $this->pageRedirection($result, $customerPhone);
    }*/

    /**
     * @param  $json
     * @param  $customerPhone
     *
     * @return string
     */
    /*public function pageRedirection($json, $customerPhone)
    {
        $decodeJson = json_decode($json, true);
        $createBill = $decodeJson["CreateBill"]["_attributes"];
        $BillId = $createBill["Id"];
        $paymentRid = $createBill["PaymentRid"];
        $data = array(
            'bill' => $BillId,
            'PaymentRid' => $paymentRid,
            'checkout' => true,
            'mode' => $this->helper->getPaymentMode(),
            "success_url" => $this->getSuccessURL(),
            "expired_url" => $this->getFailureURL(),
            "cancel_url" => $this->getFailureURL(),
            "return_url" => $this->getFailureURL()
        );
        $additionalData = [];
        if ($this->helper->getPaymentMode() === 'checkout') {
            $additionalData += ['customer_phone' => $customerPhone];
        }
        $queryString = array_merge($data, $additionalData);
        $url = 'resirection';
        $query = $this->buildQueryString($queryString);

        return $url . '?' . $query;
    }*/

    /**
     * @param  $parameters
     *
     * @return string|null
     */
    /*public function buildQueryString($parameters)
    {
        $url = null;
        $count = count($parameters);
        $i = 1;
        foreach ($parameters as $key => $value) {
            if ($i != $count) {
                $url .= $key . '=' . $value . '&';
            } else {
                $url .= $key . '=' . $value;
            }
            $i++;
        }
        return $url;
    }*/

    /**
     * @param  $url
     * @param  array|NULL $post
     * @param  array $options
     *
     * @return bool|string
     */
    function curlPost($url, array $post = null, array $options = array())
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
}
