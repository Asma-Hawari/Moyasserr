<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Controller;
/**
 * Class STCPay
 * 
 * @package Moyasser\STC\Controller
 */
abstract class STCPay extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote = false;

    /**
     * @var \Moyasser\STC\Model\STCPay
     */
    protected $stcPayModel;

    /**
     * @var \Moyasser\STC\Helper\Data
     */
    protected $stcPayHelper;

    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
	protected $orderHistoryFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
	protected  $orderModel;

    /**
     * STCPay constructor.
     * @param  \Magento\Framework\App\Action\Context  $context
     * @param  \Magento\Customer\Model\Session  $customerSession
     * @param  \Magento\Checkout\Model\Session  $checkoutSession
     * @param  \Magento\Sales\Model\OrderFactory  $orderFactory
     * @param  \Magento\Sales\Model\Order\Status\HistoryFactory  $orderHistoryFactory
     * @param  \Moyasser\STC\Model\STCPay  $stcPayModel
     * @param  \Moyasser\STC\Helper\Data  $stcPayHelper
     * @param  \Psr\Log\LoggerInterface  $logger
     * @param  \Magento\Sales\Model\Order  $orderModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Moyasser\STC\Model\STCPay $stcPayModel,
        \Moyasser\STC\Helper\Data $stcPayHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $orderModel
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
		$this->orderHistoryFactory = $orderHistoryFactory;
        $this->stcPayModel = $stcPayModel;
        $this->stcPayHelper = $stcPayHelper;
        $this->orderModel = $orderModel;
        parent::__construct($context);
    }


    /**
     * Cancel order, return quote to customer
     *
     * @param  string $errorMsg
     * @return false|string
     */
    protected function cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        $this->stcPayHelper->cancelCurrentOrder($errorMsg);
        if ($this->checkoutSession->restoreQuote()) {
            $gotoSection = 'paymentMethod';
        }

        return $gotoSection;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrderById($orderId)
    {
        $orderObject = $this->orderModel->loadByIncrementId($orderId);
        return $orderObject;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if ($this->checkoutSession->getLastRealOrderId()) {
            $order = $this->orderFactory->create()->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
            return $order;
        }
        return false;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getRealOrderId()
    {
        $orderDataModel = $this->orderModel->getCollection()->getLastItem();
        $orderId   =   $orderDataModel->getId();
        $order = $this->orderModel->load($orderId);
        return $order;
    }

    /**
     * @param  $order
     * @param  $comment
     * @return bool
     */
	protected function addOrderHistory($order,$comment){
		$history = $this->orderHistoryFactory->create()
            ->setComment($comment)
            ->setEntityName('order')
            ->setOrder($order);
			$history->save();
		return true;
	}

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->quote;
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    protected function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * @return \Moyasser\STC\Model\STCPay
     */
    protected function getMobiCashModel()
    {
        return $this->stcPayModel;
    }

    /**
     * @return \Moyasser\STC\Helper\Data
     */
    protected function getMobiCashHelper()
    {
        return $this->stcPayHelper;
    }
}
