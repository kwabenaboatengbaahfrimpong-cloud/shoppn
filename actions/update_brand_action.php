<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

$id   = filter_var($_POST['brand_id'] ?? '', FILTER_VALIDATE_INT);
$name = strip_tags(trim($_POST['brand_name'] ?? ''));

if (!$id || $id < 1) {
    $_SESSION['error'] = 'Invalid brand';
    redirect(BASE_URL . '/views/admin/brand.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required and must be 100 characters or less';
    redirect(BASE_URL . '/views/admin/brand.php?edit_id=' . $id);
}

$controller = new ProductController();
$result = $controller->updateBrand($id, $name);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/admin/brand.php?edit_id=' . $id);
}

$_SESSION['success'] = 'Brand updated.';
redirect(BASE_URL . '/views/admin/brand.php');
