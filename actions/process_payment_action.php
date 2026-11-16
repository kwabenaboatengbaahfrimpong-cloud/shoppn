<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack.php';
require_once __DIR__ . '/../core/mailer.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Paystack's pop-up sends the shopper here with ?ref=REFERENCE after paying.
// Nothing is saved until Paystack itself confirms the payment.

function payment_failed($message, $reference = '') {
    $_SESSION['error'] = $message;
    $_SESSION['failed_ref'] = $reference;
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!is_logged_in()) {
    redirect(BASE_URL . '/views/login.php');
}

$reference = trim($_GET['ref'] ?? '');

// Paystack references only use letters, numbers and - . =
if (!preg_match('/^[A-Za-z0-9.=-]{1,100}$/', $reference)) {
    payment_failed('Missing or invalid payment reference.');
}

// Page refreshed after a payment that already went through: show the same invoice.
if (!empty($_SESSION['last_paid_ref']) && $_SESSION['last_paid_ref'] === $reference) {
    redirect(BASE_URL . '/views/payment_success.php?order_id=' . (int) $_SESSION['last_order_id']);
}

// The reference must be one this site gave out in this session (views/payment.php).
// This blocks made-up references and references from someone else's payment.
$issued = $_SESSION['pay_refs'] ?? [];
if (!in_array($reference, $issued, true)) {
    payment_failed('This payment reference did not come from your checkout.', $reference);
}
// Use it once only.
$_SESSION['pay_refs'] = array_values(array_diff($issued, [$reference]));

// 1. Ask Paystack, server to server, whether this payment succeeded.
$check = verify_paystack_payment($reference);
if (!$check['ok']) {
    payment_failed($check['error'], $reference);
}

// 2. Make sure the shopper paid the full cart total, in cedis.
$controller   = new CartController();
$cart_total   = $controller->getCartTotal(get_ip());
$expected_pes = (int) round($cart_total * 100);

if ($cart_total <= 0) {
    payment_failed('Your cart is empty, so there is nothing to record. Contact us with your reference.', $reference);
}
if ($check['currency'] !== 'GHS' || $check['amount'] !== $expected_pes) {
    payment_failed('The amount paid does not match your cart total. Contact us with your reference.', $reference);
}

// 3. Save the order, its items and the payment, and empty the cart.
$result = $controller->processPayment(
    (int) $_SESSION['customer_id'],
    get_ip(),
    $reference,
    $check['amount'] / 100
);

if (!$result['success']) {
    payment_failed($result['error'], $reference);
}

$_SESSION['last_paid_ref'] = $reference;
$_SESSION['last_order_id'] = $result['order_id'];

// 4. Email the receipt. A mail failure does not undo the order.
$order = $controller->getOrderById($result['order_id'], (int) $_SESSION['customer_id']);
if ($order) {
    send_order_receipt($_SESSION['customer_email'], $_SESSION['customer_name'], $order);
}

redirect(BASE_URL . '/views/payment_success.php?order_id=' . (int) $result['order_id']);
