<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/product.php');
}

$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT);
$title    = strip_tags(trim($_POST['product_title'] ?? ''));
$price    = $_POST['product_price'] ?? '';
$desc     = strip_tags(trim($_POST['product_desc'] ?? ''));
$keywords = strip_tags(trim($_POST['product_keywords'] ?? ''));

// validate the text fields first
if (!$cat || !$brand) {
    $_SESSION['error'] = 'Please choose a category and a brand';
} elseif ($title === '' || strlen($title) > 200) {
    $_SESSION['error'] = 'Title is required and must be 200 characters or less';
} elseif (!is_numeric($price) || $price <= 0) {
    $_SESSION['error'] = 'Price must be a number greater than 0';
} elseif (strlen($desc) > 500) {
    $_SESSION['error'] = 'Description must be 500 characters or less';
} elseif (strlen($keywords) > 100) {
    $_SESSION['error'] = 'Keywords must be 100 characters or less';
} elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'Please choose a product image';
}

if (!empty($_SESSION['error'])) {
    redirect(BASE_URL . '/views/admin/product.php');
}

// then the image (type and size are checked again here, on the server)
$upload = save_product_image($_FILES['product_image']);
if (!$upload['ok']) {
    $_SESSION['error'] = $upload['error'];
    redirect(BASE_URL . '/views/admin/product.php');
}

$controller = new ProductController();
$result = $controller->addProduct($cat, $brand, $title, (float) $price, $desc, $upload['filename'], $keywords);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/admin/product.php');
}

$_SESSION['success'] = 'Product added.';
redirect(BASE_URL . '/views/admin/product.php');
