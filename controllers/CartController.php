<?php
require_once __DIR__ . '/../classes/CartClass.php';

// Middleman between the cart actions/views and CartClass. No SQL, no HTML here.
class CartController {
    private $model;

    public function __construct() {
        $this->model = new CartClass();
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        if (!$this->model->productExists($product_id)) {
            return ['success' => false, 'error' => 'That product does not exist.'];
        }
        if ($this->model->isInCart($product_id, $ip)) {
            return ['success' => false, 'error' => 'This item is already in your cart. Go to cart to update quantity.'];
        }
        if (!$this->model->addToCart($product_id, $qty, $ip, $customer_id)) {
            return ['success' => false, 'error' => 'Could not add the item, try again.'];
        }
        return ['success' => true];
    }

    public function isInCart($product_id, $ip) {
        return $this->model->isInCart($product_id, $ip);
    }

    public function getCartItems($ip) {
        return $this->model->getCartItems($ip);
    }

    public function getCartCount($ip) {
        return $this->model->getCartCount($ip);
    }

    public function getCartTotal($ip) {
        return $this->model->getCartTotal($ip);
    }

    public function removeFromCart($product_id, $ip) {
        return $this->model->removeFromCart($product_id, $ip);
    }

    public function updateQty($product_id, $new_qty, $ip) {
        return $this->model->updateQty($product_id, $new_qty, $ip);
    }

    // Called only after Paystack has confirmed the payment.
    public function processPayment($customer_id, $ip, $reference, $amount) {
        $order_id = $this->model->processPayment($customer_id, $ip, $reference, $amount);
        if (!$order_id) {
            return ['success' => false, 'error' => 'Your payment went through but the order could not be saved. Contact us with reference ' . $reference . '.'];
        }
        return ['success' => true, 'order_id' => $order_id];
    }

    public function getOrderById($order_id, $customer_id) {
        return $this->model->getOrderById($order_id, $customer_id);
    }
}
