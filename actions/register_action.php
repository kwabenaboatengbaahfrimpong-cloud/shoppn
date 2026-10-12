<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/register.php');
}

$name    = strip_tags(trim($_POST['name'] ?? ''));
$email   = trim($_POST['email'] ?? '');
$pass    = $_POST['pass'] ?? '';
$country = strip_tags(trim($_POST['country'] ?? ''));
$city    = strip_tags(trim($_POST['city'] ?? ''));
$contact = strip_tags(trim($_POST['contact'] ?? ''));

// Server-side validation. The limits match the column sizes in the customer table,
// so bad data gets a clear message here instead of a database error.
$phoneRegex = '/^[0-9+\-\s]{7,15}$/';
$countries  = ['Ghana', 'Nigeria', 'Other'];   // same options as the dropdown on the form

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email';
    redirect(BASE_URL . '/views/register.php');
}

if (strlen($email) > 50) {
    $_SESSION['error'] = 'Email is too long (50 characters at most)';
    redirect(BASE_URL . '/views/register.php');
}

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $_SESSION['error'] = 'Name must be between 2 and 100 characters';
    redirect(BASE_URL . '/views/register.php');
}

// Server-side password check, mirrors js/validate.js's passRegex exactly.
// Client-side JS can be disabled or bypassed, so this is the real gate;
// the JS copy only gives the user instant feedback before they submit.
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $pass)) {
    $_SESSION['error'] = 'Password needs at least 8 characters, with a letter and a number';
    redirect(BASE_URL . '/views/register.php');
}

if (!in_array($country, $countries, true)) {
    $_SESSION['error'] = 'Please choose a country from the list';
    redirect(BASE_URL . '/views/register.php');
}

if ($city === '' || mb_strlen($city) > 30) {
    $_SESSION['error'] = 'City is required (30 characters at most)';
    redirect(BASE_URL . '/views/register.php');
}

if (!preg_match($phoneRegex, $contact)) {
    $_SESSION['error'] = 'Enter a valid contact number (7 to 15 digits)';
    redirect(BASE_URL . '/views/register.php');
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
    redirect(BASE_URL . '/views/register.php');
}

// log the new customer straight in
$model = new CustomerClass();
$customer = $model->getCustomerByEmail($email);

session_regenerate_id(true);   // new session id once the user is logged in
$_SESSION['customer_id']    = $customer['customer_id'];
$_SESSION['customer_name']  = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role']      = (int) $customer['user_role'];

redirect(BASE_URL . '/views/account/my_account.php');
