<?php
/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */

namespace Moyasser\STC\Controller\Standard;

use Magento\Sales\Model\Order;

/**
 * Class Webhook
 * @package Moyasser\STC\Controller\Standard
 */
class Webhook extends \Moyasser\STC\Controller\STCPay
{
    public function execute()
    {
        $transId = isset($_GET['transid']) ? $_GET['transId'] : null;
        $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $order = $this->getOrderById($orderId);
        if ($order) {
            if ($status == 'SUCCESS') {
                $order->setStatus(Order::STATE_PROCESSING);
                $order->setState(Order::STATE_PROCESSING);
                $order->addStatusToHistory($order->getStatus(), 'Order processed successfully with Fatora Transaction id reference' . $transId);
                $order->save($order);
                $this->generateInvoice($order);
            }
        }

    }

    /**
     * @param Order $order
     */
    public function generateInvoice(Order $order)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $invoiceService = $objectManager->get('Magento\Sales\Model\Service\InvoiceService');
        $transaction = $objectManager->get('Magento\Framework\DB\Transaction');
        $invoiceSender= $objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');

        if ($order->canInvoice()) {
            $invoice = $invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $invoiceSender->send($invoice);
            $order->addStatusHistoryComment(
                __('Notified customer about invoice creation #%1.', $invoice->getId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }
}