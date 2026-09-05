<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Order;
use App\Models\Product;

class OrdersController extends Controller
{
    public function index()
    {
        $token = APIMiddleware::protect();
        $orderModel = new Order();
        $orders = $orderModel->getByUserId((int) $token['user_id']);

        APIResponse::success(['orders' => $orders]);
    }

    public function show($id = null)
    {
        $token = APIMiddleware::protect();
        $orderModel = new Order();
        $order = $orderModel->find((int) $id);

        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) $token['user_id']) {
            APIResponse::error('Order not found or access denied.', 404);
        }

        $items = $orderModel->getItems((int) $id);
        APIResponse::success(['order' => $order, 'items' => $items]);
    }

    public function place()
    {
        $token = APIMiddleware::protect();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $items = $data['items'] ?? [];
        $vehicleId = !empty($data['vehicle_id']) ? (int) $data['vehicle_id'] : null;
        $address = trim((string) ($data['address'] ?? ''));

        if (empty($items) || !is_array($items)) {
            APIResponse::error('Order items are required.', 422);
        }

        $productModel = new Product();
        $cartItems = [];
        $total = 0;

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = max(1, (int) ($item['quantity'] ?? 0));
            if ($productId <= 0 || $quantity <= 0) {
                APIResponse::error('Invalid order item provided.', 422);
            }

            $product = $productModel->findById($productId);
            if (!$product || (int) ($product['status'] ?? 0) !== 1) {
                APIResponse::error('Product not available: ' . $productId, 404);
            }

            if ((int) ($product['stock'] ?? 0) < $quantity) {
                APIResponse::error('Insufficient stock for product: ' . $product['title_fa'], 409);
            }

            $cartItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => (float) ($product['price'] ?? 0),
            ];
            $total += ((float) ($product['price'] ?? 0) * $quantity);
        }

        $orderModel = new Order();
        $orderId = $orderModel->createOrder([
            'user_id' => (int) $token['user_id'],
            'vehicle_id' => $vehicleId,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'address' => $address,
        ]);

        if (!$orderId) {
            APIResponse::error('Could not create order.', 500);
        }

        foreach ($cartItems as $item) {
            $orderModel->addItem([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
            $productModel->decreaseStock((int) $item['product_id'], (int) $item['quantity'], 'Order #' . $orderId);
        }

        APIResponse::success(['order_id' => $orderId, 'total_amount' => $total]);
    }
}
