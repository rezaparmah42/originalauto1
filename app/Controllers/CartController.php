<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart as CartModel;
use App\Models\Product;
use App\Models\Vehicle;

class CartController extends Controller
{
    private $productModel;
    private $vehicleModel;
    private $cartModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->vehicleModel = new Vehicle();
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        $items = $this->getCartItems();
        $totals = $this->calculateTotals($items);
        $vehicles = [];

        if (isCustomerLoggedIn()) {
            $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
        }

        $this->view('cart/index', [
            'items' => $items,
            'subtotal' => $totals['subtotal'],
            'total' => $totals['total'],
            'vehicles' => $vehicles,
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/cart');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/cart');
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        $product = $this->productModel->findById($productId);
        if (!$product) {
            error('محصول یافت نشد.');
            redirect(SITE_URL . '/cart');
        }

        if ((int) ($product['stock'] ?? 0) < $quantity) {
            error('موجودی کافی نیست.');
            redirect(SITE_URL . '/products/' . rawurlencode($product['slug'] ?? $product['id']));
        }

        $this->cartModel->addItem($this->getCartKey(), [
            'product_id' => $productId,
            'title' => $product['title_fa'] ?? $product['title_en'] ?? '',
            'price' => (float) ($product['price'] ?? 0),
            'quantity' => $quantity,
            'stock' => (int) ($product['stock'] ?? 0),
        ]);
        success('محصول به سبد خرید اضافه شد.');
        redirect(SITE_URL . '/cart');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/cart');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/cart');
        }

        foreach ($_POST['quantities'] ?? [] as $key => $quantity) {
            $quantity = max(1, (int) $quantity);
            $productId = (int) str_replace('p_', '', $key);
            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->cartModel->removeItem($this->getCartKey(), $key);
                continue;
            }

            $safeQuantity = min($quantity, max(1, (int) ($product['stock'] ?? 1)));
            $this->cartModel->updateItem($this->getCartKey(), $key, $safeQuantity);
        }
        success('سبد خرید به‌روزرسانی شد.');
        redirect(SITE_URL . '/cart');
    }

    public function ajaxUpdate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        if (!verify_csrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF']);
            exit;
        }

        $itemKey = trim((string) ($_POST['item_key'] ?? ''));
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($itemKey === '') {
            echo json_encode(['success' => false, 'message' => 'Missing item key']);
            exit;
        }

        $cart = $this->cartModel->getCartData($this->getCartKey());
        if (!isset($cart[$itemKey])) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }

        $productId = (int) ($cart[$itemKey]['product_id'] ?? 0);
        $product = $this->productModel->findById($productId);
        if (!$product) {
            $this->cartModel->removeItem($this->getCartKey(), $itemKey);
            echo json_encode(['success' => false, 'message' => 'Product removed']);
            exit;
        }

        $safeQuantity = min($quantity, max(1, (int) ($product['stock'] ?? 1)));
        $this->cartModel->updateItem($this->getCartKey(), $itemKey, $safeQuantity);
        $items = $this->getCartItems();
        $totals = $this->calculateTotals($items);

        echo json_encode([
            'success' => true,
            'quantity' => $safeQuantity,
            'subtotal' => $totals['subtotal'],
            'total' => $totals['total'],
        ]);
        exit;
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/cart');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/cart');
        }

        $key = $_POST['item_key'] ?? '';
        $this->cartModel->removeItem($this->getCartKey(), $key);
        success('محصول از سبد خرید حذف شد.');
        redirect(SITE_URL . '/cart');
    }

    private function getCartKey()
    {
        return $this->cartModel->getCartKey(isCustomerLoggedIn() ? currentCustomerId() : null);
    }

    private function getCartData()
    {
        return $this->cartModel->getCartData($this->getCartKey());
    }

    private function saveCartData(array $cart)
    {
        $this->cartModel->saveCartData($this->getCartKey(), $cart);
    }

    private function getCartItems()
    {
        return $this->cartModel->getItems($this->getCartKey(), $this->productModel);
    }

    private function calculateTotals(array $items)
    {
        return $this->cartModel->calculateTotals($items);
    }
}
