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

    // ── Payment ─────────────────────────────────────────────────────

    // Turns the cart into a paid order. Runs as one transaction:
    // either every step saves, or none of them do.
    // $amount is the amount Paystack confirmed, in cedis.
    // Returns the new order_id, or false if anything failed.
    public function processPayment($customer_id, $ip, $reference, $amount) {
        $this->conn->begin_transaction();
        try {
            // 1. invoice number
            $invoice_no = mt_rand(100000, 999999);

            // 2. the order row
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                 VALUES (?, ?, CURDATE(), 'paid')"
            );
            $stmt->bind_param('ii', $customer_id, $invoice_no);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            // 3. copy the cart rows into orderdetails
            $stmt = $this->conn->prepare(
                "INSERT INTO orderdetails (order_id, product_id, qty)
                 SELECT ?, p_id, qty FROM cart WHERE ip_add = ?"
            );
            $stmt->bind_param('is', $order_id, $ip);
            $stmt->execute();
            $copied = $stmt->affected_rows;
            $stmt->close();

            if ($copied < 1) {
                throw new Exception('Cart was empty when payment was recorded.');
            }

            // record the payment against the order
            $stmt = $this->conn->prepare(
                "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                 VALUES (?, ?, ?, 'GHS', CURDATE())"
            );
            $stmt->bind_param('dii', $amount, $customer_id, $order_id);
            $stmt->execute();
            $stmt->close();

            // 4. empty the cart
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE ip_add = ?");
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();

            // 5. hand back the new order id
            return $order_id;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('processPayment failed for ref ' . $reference . ': ' . $e->getMessage());
            return false;
        }
    }

    // One order with its items and payment. The customer_id check means
    // a shopper only sees their own invoices, even if they change the
    // order_id in the address bar.
    public function getOrderById($order_id, $customer_id) {
        $orders = $this->fetchRows(
            "SELECT orders.*, payment.amt, payment.currency, payment.payment_date
             FROM orders
             LEFT JOIN payment ON payment.order_id = orders.order_id
             WHERE orders.order_id = ? AND orders.customer_id = ?",
            'ii', [$order_id, $customer_id]
        );
        if (count($orders) === 0) {
            return false;
        }

        $order = $orders[0];
        $order['items'] = $this->fetchRows(
            "SELECT orderdetails.product_id, orderdetails.qty,
                    products.product_title, products.product_price, products.product_image
             FROM orderdetails
             JOIN products ON products.product_id = orderdetails.product_id
             WHERE orderdetails.order_id = ?",
            'i', [$order_id]
        );
        return $order;
    }
}
