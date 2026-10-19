<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$ids = $_POST['remove'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
    $_SESSION['error'] = 'Tick at least one item to remove.';
    redirect(BASE_URL . '/views/cart.php');
}

$controller = new CartController();
$removed = 0;

foreach ($ids as $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        continue;
    }
    if ($controller->removeFromCart($id, get_ip())) {
        $removed++;
    }
}

if ($removed === 0) {
    $_SESSION['error'] = 'Nothing was removed.';
} else {
    $_SESSION['success'] = 'Cart updated. Removed ' . $removed . ($removed === 1 ? ' item.' : ' items.');
}
redirect(BASE_URL . '/views/cart.php');
