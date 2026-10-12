<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

$name = strip_tags(trim($_POST['brand_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required and must be 100 characters or less';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$controller = new ProductController();
$result = $controller->addBrand($name);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/admin/brand.php');
}

$_SESSION['success'] = 'Brand added.';
redirect(BASE_URL . '/views/admin/brand.php');
