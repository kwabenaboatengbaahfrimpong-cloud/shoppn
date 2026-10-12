<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/product.php');
}

$id       = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT);
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT);
$title    = strip_tags(trim($_POST['product_title'] ?? ''));
$price    = $_POST['product_price'] ?? '';
$desc     = strip_tags(trim($_POST['product_desc'] ?? ''));
$keywords = strip_tags(trim($_POST['product_keywords'] ?? ''));

if (!$id || $id < 1) {
    $_SESSION['error'] = 'Invalid product';
    redirect(BASE_URL . '/views/admin/product.php');
}

$back = BASE_URL . '/views/admin/product.php?edit_id=' . $id;

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
}

if (!empty($_SESSION['error'])) {
    redirect($back);
}

$controller = new ProductController();
$existing = $controller->getProductById($id);

if (!$existing) {
    $_SESSION['error'] = 'That product no longer exists';
    redirect(BASE_URL . '/views/admin/product.php');
}

// keep the old image unless a new one was chosen
$filename = $existing['product_image'];

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $upload = save_product_image($_FILES['product_image']);
    if (!$upload['ok']) {
        $_SESSION['error'] = $upload['error'];
        redirect($back);
    }
    $filename = $upload['filename'];
}

$result = $controller->updateProduct($id, $cat, $brand, $title, (float) $price, $desc, $filename, $keywords);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    redirect($back);
}

$_SESSION['success'] = 'Product updated.';
redirect(BASE_URL . '/views/admin/product.php');
