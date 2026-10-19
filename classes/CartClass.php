<?php
require_once __DIR__ . '/../core/db_class.php';

// All cart queries live here. No HTML, no $_POST, no echo.
// A cart row belongs to a visitor by IP address, so guests can shop too.
class CartClass extends Database {

    private function fetchRows($sql, $types = '', $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function productExists($product_id) {
        $rows = $this->fetchRows("SELECT product_id FROM products WHERE product_id = ?", 'i', [$product_id]);
        return count($rows) > 0;
    }

    public function isInCart($product_id, $ip) {
        $rows = $this->fetchRows(
            "SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?",
            'is', [$product_id, $ip]
        );
        return count($rows) > 0;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isii', $product_id, $ip, $customer_id, $qty);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getCartItems($ip) {
        return $this->fetchRows(
            "SELECT cart.*, products.product_title, products.product_price, products.product_image
             FROM cart
             JOIN products ON cart.p_id = products.product_id
             WHERE cart.ip_add = ?
             ORDER BY products.product_title ASC",
            's', [$ip]
        );
    }

    public function getCartCount($ip) {
        $rows = $this->fetchRows("SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?", 's', [$ip]);
        return (int) $rows[0]['total'];
    }

    public function getCartTotal($ip) {
        $total = 0;
        foreach ($this->getCartItems($ip) as $item) {
            $total += $item['qty'] * $item['product_price'];
        }
        return $total;
    }

    public function removeFromCart($product_id, $ip) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param('is', $product_id, $ip);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateQty($product_id, $new_qty, $ip) {
        if ($new_qty < 1) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param('iis', $new_qty, $product_id, $ip);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
