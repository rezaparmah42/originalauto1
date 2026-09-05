<?php

namespace App\Models;

class Cart
{
    public function getCartKey($customerId = null)
    {
        if ($customerId) {
            return 'cart_' . (int) $customerId;
        }

        return 'cart';
    }

    public function getCartData($cartKey)
    {
        $cart = $_SESSION[$cartKey] ?? [];
        if (!is_array($cart)) {
            return [];
        }

        return $cart;
    }

    public function saveCartData($cartKey, array $cart)
    {
        $_SESSION[$cartKey] = $cart;
    }

    public function addItem($cartKey, array $item)
    {
        $cart = $this->getCartData($cartKey);
        $key = 'p_' . (int) ($item['product_id'] ?? 0);
        $existingQuantity = (int) ($cart[$key]['quantity'] ?? 0);
        $cart[$key] = [
            'product_id' => (int) ($item['product_id'] ?? 0),
            'title' => trim((string) ($item['title'] ?? '')),
            'price' => (float) ($item['price'] ?? 0),
            'quantity' => $existingQuantity + (int) ($item['quantity'] ?? 1),
            'stock' => (int) ($item['stock'] ?? 0),
        ];

        $this->saveCartData($cartKey, $cart);

        return $cart;
    }

    public function updateItem($cartKey, $itemKey, $quantity)
    {
        $cart = $this->getCartData($cartKey);
        if (!isset($cart[$itemKey])) {
            return $cart;
        }

        $cart[$itemKey]['quantity'] = max(1, (int) $quantity);
        $this->saveCartData($cartKey, $cart);

        return $cart;
    }

    public function removeItem($cartKey, $itemKey)
    {
        $cart = $this->getCartData($cartKey);
        unset($cart[$itemKey]);
        $this->saveCartData($cartKey, $cart);

        return $cart;
    }

    public function clear($cartKey)
    {
        unset($_SESSION[$cartKey]);
    }

    public function getItems($cartKey, Product $productModel)
    {
        $cart = $this->getCartData($cartKey);
        $items = [];

        foreach ($cart as $key => $item) {
            $product = $productModel->findById((int) ($item['product_id'] ?? 0));
            if (!$product) {
                $this->removeItem($cartKey, $key);
                continue;
            }

            $items[] = [
                'key' => $key,
                'product_id' => (int) ($item['product_id'] ?? 0),
                'title' => $item['title'] ?? ($product['title_fa'] ?? $product['title_en'] ?? ''),
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                'price' => (float) ($item['price'] ?? $product['price'] ?? 0),
                'stock' => (int) ($product['stock'] ?? 0),
            ];
        }

        return $items;
    }

    public function calculateTotals(array $items)
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1);
        }

        return [
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }
}
