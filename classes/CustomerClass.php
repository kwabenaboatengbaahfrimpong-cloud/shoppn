<?php
require_once __DIR__ . '/../core/db_class.php';

// All customer-related database work lives here. No HTML, no $_POST, no echo —
// just queries in, data out.
class CustomerClass extends Database {

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT customer_email FROM customer WHERE customer_email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function addCustomer($name, $email, $pass, $country, $city, $contact) {
        // PASSWORD HASHING: never store $pass itself. password_hash() with
        // PASSWORD_BCRYPT one-way hashes it (with a random salt built in),
        // so even with full database access no one can read the real password
        // back out. login() below checks it with password_verify(), never ==.
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssssss', $name, $email, $hash, $country, $city, $contact);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getCustomerByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row; // false if no match
    }

    public function login($email, $pass) {
        $customer = $this->getCustomerByEmail($email);

        if (!$customer) {
            return false;
        }

        // PASSWORD CHECK: password_verify() re-hashes the submitted password
        // the same way and compares, it never reverses the stored hash.
        if (!password_verify($pass, $customer['customer_pass'])) {
            return false;
        }

        return $customer;
    }
}
