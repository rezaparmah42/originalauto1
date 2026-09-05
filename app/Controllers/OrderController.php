<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart as CartModel;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vehicle;

class OrderController extends Controller
{
    private $orderModel;
    private $productModel;
    private $vehicleModel;
    private $cartModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->vehicleModel = new Vehicle();
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        requireCustomer();
        $this->view('orders/history', [
            'orders' => $this->orderModel->getByUserId(currentCustomerId()),
        ]);
    }

    public function history()
    {
        requireCustomer();
        $this->view('orders/history', [
            'orders' => $this->orderModel->getByUserId(currentCustomerId()),
        ]);
    }

    public function show($id)
    {
        requireCustomer();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/orders/history');
        }

        $items = $this->orderModel->getItems((int) $id);
        $this->view('orders/show', ['order' => $order, 'items' => $items]);
    }

    public function checkout()
    {
        requireCustomer();

        $cartItems = $this->getCartItems();
        if (empty($cartItems)) {
            redirect(SITE_URL . '/cart');
        }

        $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
        $this->view('cart/checkout', [
            'items' => $cartItems,
            'vehicles' => $vehicles,
            'subtotal' => $this->calculateSubtotal($cartItems),
            'total' => $this->calculateSubtotal($cartItems),
        ]);
    }

    public function placeOrder()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/orders');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/cart');
        }

        $cartItems = $this->getCartItems();
        if (empty($cartItems)) {
            error('سبد خرید خالی است.');
            redirect(SITE_URL . '/cart');
        }

        $subtotal = $this->calculateSubtotal($cartItems);
        $vehicleId = !empty($_POST['vehicle_id']) ? (int) $_POST['vehicle_id'] : null;
        $customerName = trim((string) ($_POST['customer_name'] ?? ''));
        $mobile = trim((string) ($_POST['mobile'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $postalCode = trim((string) ($_POST['postal_code'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $model = trim((string) ($_POST['model'] ?? ''));
        $year = trim((string) ($_POST['year'] ?? ''));

        if ($customerName === '' || $mobile === '' || $address === '') {
            error('نام، موبایل و آدرس تحویل الزامی است.');
            redirect(SITE_URL . '/checkout');
        }

        $this->orderModel->db->beginTransaction();
        try {
            foreach ($cartItems as $item) {
                $product = $this->productModel->findById((int) ($item['product_id'] ?? 0));
                if (!$product) {
                    throw new \RuntimeException('یکی از محصولات در سبد خرید وجود ندارد.');
                }
                if ((int) ($product['stock'] ?? 0) < (int) ($item['quantity'] ?? 1)) {
                    throw new \RuntimeException('موجودی محصول ' . ($product['title_fa'] ?? $product['title_en'] ?? '') . ' کافی نیست.');
                }
            }

            $orderId = $this->orderModel->createOrder([
                'user_id' => currentCustomerId(),
                'vehicle_id' => $vehicleId,
                'total_amount' => $subtotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'customer_name' => $customerName,
                'mobile' => $mobile,
                'address' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
            ]);

            if (!$orderId) {
                throw new \RuntimeException('ثبت سفارش با خطا مواجه شد.');
            }

            foreach ($cartItems as $item) {
                $product = $this->productModel->findById((int) ($item['product_id'] ?? 0));
                if (!$product) {
                    throw new \RuntimeException('محصول سفارش نامعتبر است.');
                }

                $this->orderModel->addItem([
                    'order_id' => $orderId,
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'price' => (float) ($item['price'] ?? 0),
                    'price_snapshot' => (float) ($item['price'] ?? 0),
                ]);
            }

            $this->clearCart();
            $this->orderModel->db->commit();
            $_SESSION['last_order_id'] = $orderId;
            redirect(SITE_URL . '/payment/start/' . $orderId);
        } catch (\Throwable $e) {
            try { $this->orderModel->db->rollBack(); } catch (\Throwable $rollBackException) {}
            error($e->getMessage());
            redirect(SITE_URL . '/cart');
        }
    }

    public function success($id)
    {
        requireCustomer();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/orders/history');
        }

        $this->view('orders/success', ['order' => $order]);
    }

    public function successCheckout($id = null)
    {
        requireCustomer();
        $orderId = (int) ($id ?? ($_GET['order_id'] ?? ($_SESSION['last_order_id'] ?? 0)));
        $order = $this->orderModel->find($orderId);
        if ($order && (int) ($order['user_id'] ?? 0) === (int) currentCustomerId()) {
            $this->view('orders/success', ['order' => $order]);
            return;
        }

        redirect(SITE_URL . '/orders/history');
    }

    public function failedCheckout($id = null)
    {
        requireCustomer();
        $orderId = (int) ($id ?? ($_GET['order_id'] ?? ($_SESSION['last_order_id'] ?? 0)));
        $order = $this->orderModel->find($orderId);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/orders/history');
        }

        $this->view('orders/failed', ['order' => $order]);
    }

    private function getCartItems()
    {
        return $this->cartModel->getItems($this->getCartKey(), $this->productModel);
    }

    private function calculateSubtotal(array $items)
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
        }

        return $subtotal;
    }

    private function getCartKey()
    {
        return $this->cartModel->getCartKey(isCustomerLoggedIn() ? currentCustomerId() : null);
    }

    private function clearCart()
    {
        $this->cartModel->clear($this->getCartKey());
    }
}
