<?php require_once __DIR__ . '/../../core/core.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoppn</title>
    <link rel="stylesheet" href="/shoppn/css/style.css">
</head>
<body>
<header class="site-header">
    <a href="/shoppn/index.php" class="logo">Shoppn</a>

    <nav>
        <a href="/shoppn/index.php">Home</a>

        <?php if (is_admin()): ?>
            <a href="/shoppn/views/admin/brand.php">Brands</a>
            <a href="/shoppn/views/admin/category.php">Category</a>
        <?php endif; ?>

        <?php if (is_logged_in()): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
            <a href="/shoppn/views/account/my_account.php">My Account</a>
            <a href="/shoppn/logout.php">Logout</a>
        <?php else: ?>
            <a href="/shoppn/views/register.php">Register</a>
            <a href="/shoppn/views/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
