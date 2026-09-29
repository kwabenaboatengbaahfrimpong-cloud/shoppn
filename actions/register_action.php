<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/shoppn/views/register.php');
}

$name    = strip_tags(trim($_POST['name'] ?? ''));
$email   = trim($_POST['email'] ?? '');
$pass    = $_POST['pass'] ?? '';
$country = strip_tags(trim($_POST['country'] ?? ''));
$city    = strip_tags(trim($_POST['city'] ?? ''));
$contact = strip_tags(trim($_POST['contact'] ?? ''));

// basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email';
    redirect('/shoppn/views/register.php');
}

if (strlen($email) > 50) {
    $_SESSION['error'] = 'Email is too long';
    redirect('/shoppn/views/register.php');
}

if (strlen($name) < 2 || strlen($pass) < 8) {
    $_SESSION['error'] = 'Name must be at least 2 characters and password at least 8';
    redirect('/shoppn/views/register.php');
}

$controller = new CustomerController();
$result = $controller->register([
    'name'    => $name,
    'email'   => $email,
    'pass'    => $pass,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact,
]);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect('/shoppn/views/register.php');
}

// log the new customer straight in
$model = new CustomerClass();
$customer = $model->getCustomerByEmail($email);

$_SESSION['customer_id']    = $customer['customer_id'];
$_SESSION['customer_name']  = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role']      = (int) $customer['user_role'];

redirect('/shoppn/views/account/my_account.php');
