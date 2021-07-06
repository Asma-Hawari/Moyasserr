<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface as UrlInterface;
use \Moyasser\STC\Helper\Data;

/**
 * Class StcConfigProvider
 *
 * @package Moyasser\STC\Model
 */
class StcCashConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    protected $methodCode = "mysStc";

    /**
     * @var \Magento\Payment\Model\MethodInterface
     */
    protected $method;

    /**
 * @var UrlInterface
 */
    protected $urlBuilder;

    /**
     * @var  \Moyasser\STC\Helper\Data
     */
    protected $moyasarHelper;



    /**
     * StcConfigProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @param \Moyasser\STC\Helper\Data $helper
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        \Moyasser\STC\Helper\Data $helper
    )
    {
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->urlBuilder = $urlBuilder;
        $this->moyasarHelper = $helper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'mysStc' => [
                    'redirectUrl' => $this->getRedirectUrl(),
                    'publishable_api_key' => $this->moyasarHelper->getPublishableKey(),
                    'payment_url'=> $this->moyasarHelper->buildMoyasarUrl('payments')
                ]
            ]
        ] : [];
    }

    /**
     * @return string
     */
    protected function getRedirectUrl()
    {
        return $this->urlBuilder->getUrl('Moyasser_STC/Standard/Redirect', ['_secure' => true]);
    }

    /**
     * @return mixed
     */
    protected function getFormData()
    {
        return $this->method->getRedirectUrl();
    }
    
}
