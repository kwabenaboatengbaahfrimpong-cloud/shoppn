<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/category.php');
}

$id   = filter_var($_POST['cat_id'] ?? '', FILTER_VALIDATE_INT);
$name = strip_tags(trim($_POST['cat_name'] ?? ''));

if (!$id || $id < 1) {
    $_SESSION['error'] = 'Invalid category';
    redirect(BASE_URL . '/views/admin/category.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required and must be 100 characters or less';
    redirect(BASE_URL . '/views/admin/category.php?edit_id=' . $id);
}

$controller = new ProductController();
$result = $controller->updateCategory($id, $name);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/admin/category.php?edit_id=' . $id);
}

$_SESSION['success'] = 'Category updated.';
redirect(BASE_URL . '/views/admin/category.php');
