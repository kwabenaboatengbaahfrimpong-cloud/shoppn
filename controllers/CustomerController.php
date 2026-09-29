<?php
require_once __DIR__ . '/../classes/CustomerClass.php';

// Sits between the actions (which handle the raw form data) and the
// CustomerClass (which talks to the database). No SQL, no HTML here.
class CustomerController {
    private $model;

    public function __construct() {
        $this->model = new CustomerClass();
    }

    public function register($data) {
        if ($this->model->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $ok = $this->model->addCustomer(
            $data['name'],
            $data['email'],
            $data['pass'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if (!$ok) {
            return ['success' => false, 'error' => 'Could not create account, try again'];
        }

        return ['success' => true];
    }

    public function login($email, $pass) {
        $customer = $this->model->login($email, $pass);

        if (!$customer) {
            return ['success' => false, 'error' => 'Wrong email or password'];
        }

        return ['success' => true, 'customer' => $customer];
    }
}
