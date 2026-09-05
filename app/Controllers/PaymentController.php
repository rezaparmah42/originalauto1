<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;

class PaymentController extends Controller
{
    private $orderModel;
    private $paymentModel;
    private $productModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->paymentModel = new Payment();
        $this->productModel = new Product();
    }

    public function start($id = null)
    {
        requireCustomer();

        $orderId = (int) ($id ?? ($_GET['order_id'] ?? 0));
        $order = $this->orderModel->find($orderId);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/orders/history');
        }

        $existing = $this->paymentModel->findByOrderId($orderId);
        if ($existing && ($existing['status'] ?? 'pending') === 'paid') {
            redirect(SITE_URL . '/payment/result?order_id=' . $orderId . '&status=paid');
        }

        $transactionId = 'txn_' . $orderId . '_' . time();
        $paymentId = $this->paymentModel->createPayment([
            'order_id' => $orderId,
            'user_id' => currentCustomerId(),
            'amount' => (float) ($order['total_amount'] ?? 0),
            'transaction_id' => $transactionId,
            'status' => 'pending',
            'gateway' => 'mock_gateway',
        ]);

        if (!$paymentId) {
            error('ایجاد پرداخت با خطا مواجه شد.');
            redirect(SITE_URL . '/orders/history');
        }

        $paymentToken = $this->paymentModel->buildCallbackToken($orderId, (int) $paymentId, (int) currentCustomerId(), (float) ($order['total_amount'] ?? 0));

        redirect(SITE_URL . '/payment/callback?order_id=' . $orderId . '&payment_id=' . $paymentId . '&status=paid&token=' . urlencode($paymentToken));
    }

    public function callback()
    {
        requireCustomer();

        $orderId = (int) ($_GET['order_id'] ?? 0);
        $status = strtolower(trim((string) ($_GET['status'] ?? '')));
        $paymentId = (int) ($_GET['payment_id'] ?? 0);
        $token = trim((string) ($_GET['token'] ?? ''));

        $order = $this->orderModel->find($orderId);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/orders/history');
            return;
        }

        $payment = $this->paymentModel->findByOrderId($orderId);
        if (!$payment || (int) ($payment['id'] ?? 0) !== $paymentId) {
            error('اطلاعات پرداخت نامعتبر است.');
            redirect(SITE_URL . '/orders/history');
            return;
        }

        $expectedToken = $this->paymentModel->buildCallbackToken(
            $orderId,
            (int) ($payment['id'] ?? 0),
            (int) ($payment['user_id'] ?? 0),
            (float) ($payment['amount'] ?? 0)
        );

        if (!hash_equals($expectedToken, $token)) {
            error('تأیید پرداخت نامعتبر است.');
            redirect(SITE_URL . '/orders/history');
            return;
        }

        if (!in_array($status, ['paid', 'pending', 'failed'], true)) {
            $status = ($payment['status'] ?? 'pending') === 'paid' ? 'paid' : 'pending';
        }

        if ($status === 'paid' && (($payment['status'] ?? 'pending') !== 'paid' || !$this->orderModel->isPaid($orderId))) {
            $stockProcessed = $this->productModel->processPaidOrderStock($orderId);
            if (!$stockProcessed) {
                error('ثبت موجودی سفارش با خطا مواجه شد. پرداخت لغو شد.');
                redirect(SITE_URL . '/orders/history');
                return;
            }
        }

        $this->paymentModel->updateStatus((int) $payment['id'], $status);
        $this->orderModel->updatePaymentStatus($orderId, $status);

        redirect(SITE_URL . '/checkout/success?order_id=' . $orderId . '&status=' . urlencode($status));
    }

    public function result()
    {
        requireCustomer();

        $orderId = (int) ($_GET['order_id'] ?? 0);
        $status = trim($_GET['status'] ?? 'pending');
        $order = $this->orderModel->find($orderId);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/orders/history');
        }

        $this->view('payments/result', [
            'order' => $order,
            'status' => $status,
        ]);
    }
}
