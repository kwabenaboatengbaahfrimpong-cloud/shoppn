<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$qtys = $_POST['qty'] ?? [];
$ids  = $_POST['update'] ?? [];

if (!is_array($qtys) || !is_array($ids) || count($ids) === 0 || count($qtys) !== count($ids)) {
    $_SESSION['error'] = 'There is nothing to update.';
    redirect(BASE_URL . '/views/cart.php');
}

// check every quantity first, so a bad one does not leave the cart half updated
$changes = [];
foreach ($ids as $i => $id) {
    $id  = filter_var($id, FILTER_VALIDATE_INT);
    $qty = filter_var(trim($qtys[$i] ?? ''), FILTER_VALIDATE_INT);
    if (!$id || $id < 1 || !$qty || $qty < 1 || $qty > 99) {
        $_SESSION['error'] = 'Each quantity must be a whole number from 1 to 99.';
        redirect(BASE_URL . '/views/cart.php');
    }
    $changes[$id] = $qty;
}

$controller = new CartController();
foreach ($changes as $id => $qty) {
    $controller->updateQty($id, $qty, get_ip());
}

$_SESSION['success'] = 'Quantities updated.';
redirect(BASE_URL . '/views/cart.php');
