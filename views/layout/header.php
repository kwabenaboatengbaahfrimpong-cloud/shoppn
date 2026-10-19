<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

$cart_controller = new CartController();
$cart_count = $cart_controller->getCartCount(get_ip());
$cart_total = $cart_controller->getCartTotal(get_ip());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
<header class="site-header">
    <a href="<?php echo BASE_URL; ?>/index.php" class="logo">Shoppn</a>

    <form class="search-form" action="<?php echo BASE_URL; ?>/views/search_results.php" method="GET">
        <input type="text" name="user_query" placeholder="Search products"
               value="<?php echo htmlspecialchars($_GET['user_query'] ?? ''); ?>">
        <button type="submit">Search</button>
    </form>

    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>

        <?php if (is_admin()): ?>
            <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Brands</a>
            <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Category</a>
            <a href="<?php echo BASE_URL; ?>/views/admin/product.php">Products</a>
        <?php endif; ?>

        <?php if (is_logged_in()): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
            <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/views/register.php">Register</a>
            <a href="<?php echo BASE_URL; ?>/views/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<div class="cart-bar">
    Shopping Cart &mdash; Total Items: <?php echo $cart_count; ?> | Total Price: GHS <?php echo number_format($cart_total, 2); ?> |
    <a href="<?php echo BASE_URL; ?>/views/cart.php">Go to Cart</a>
</div>
