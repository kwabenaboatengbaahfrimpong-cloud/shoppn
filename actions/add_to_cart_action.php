<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// where to send the shopper back to: the page they came from, if it is on this site
$back = BASE_URL . '/index.php';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$ref_path = parse_url($referer, PHP_URL_PATH);
if ($ref_path && strpos($ref_path, BASE_URL . '/') === 0) {
    $back = $referer;
}

$product_id = filter_var($_GET['add_cart'] ?? '', FILTER_VALIDATE_INT);
if (!$product_id || $product_id < 1) {
    $_SESSION['error'] = 'That product could not be added.';
    redirect($back);
}

$customer_id = is_logged_in() ? (int) $_SESSION['customer_id'] : null;

$controller = new CartController();
$result = $controller->addToCart($product_id, 1, get_ip(), $customer_id);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect($back);
}

$_SESSION['success'] = 'Item added to your cart.';
redirect($back);
