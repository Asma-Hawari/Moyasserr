<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Moyasser\STC\Helper
 */
class Data extends AbstractHelper
{

    /**
     * @var mixed|string
     */
    const XML_PATH_SecretKey = "payment/mysStc/SecretKey";

    /**
     * @var mixed|string
     */
    const XML_PATH_PublishableKey = "payment/mysStc/PublishableKey";

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param  $comment
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelCurrentOrder($comment) {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function restoreQuote() {
        return $this->session->restoreQuote();
    }

    /**
     * @param  $route
     * @param  array $params
     *
     * @return string
     */
    public function getUrl($route, $params = []) {
        return $this->_getUrl($route, $params);
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSuccessURL()
    {
        return $this->storeManager->getStore()->getBaseUrl() . "index.php/checkout/onepage/success";
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFailureURL()
    {
        return $this->storeManager->getStore()->getBaseUrl() . "index.php/checkout/onepage/failure";
    }


    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getConfig(self::XML_PATH_SecretKey);
    }

    /**
     * @return mixed
     */
    public function getPublishableKey()
    {
        return $this->getConfig(self::XML_PATH_PublishableKey);
    }
    
    /**
     * @return mixed
     */
    public function buildMoyasarUrl($path)
    {
        $isStaging = false;
        $base = 'https://api.moyasar.com/v1/';

        if ($isStaging) {
            $base = 'https://apimig.moyasar.com/v1/';
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}
