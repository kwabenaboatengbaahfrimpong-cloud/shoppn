<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/category.php');
}

$name = strip_tags(trim($_POST['cat_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required and must be 100 characters or less';
    redirect(BASE_URL . '/views/admin/category.php');
}

$controller = new ProductController();
$result = $controller->addCategory($name);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/admin/category.php');
}

$_SESSION['success'] = 'Category added.';
redirect(BASE_URL . '/views/admin/category.php');
