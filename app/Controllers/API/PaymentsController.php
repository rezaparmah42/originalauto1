<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Payment;
use App\Models\Order;

class PaymentsController extends Controller
{
    public function show($id = null)
    {
        $token = APIMiddleware::protect();
        $paymentModel = new Payment();
        $payment = $paymentModel->findByOrderId((int) $id);

        if (!$payment) {
            APIResponse::error('Payment not found.', 404);
        }

        $orderModel = new Order();
        $order = $orderModel->find((int) $payment['order_id']);

        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) $token['user_id']) {
            APIResponse::error('Payment access denied.', 403);
        }

        APIResponse::success(['payment' => $payment]);
    }
}
