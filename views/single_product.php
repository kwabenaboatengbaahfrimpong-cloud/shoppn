<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// a valid ?pro_id=N is required, otherwise go back home
$id = filter_var($_GET['pro_id'] ?? '', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    redirect(BASE_URL . '/index.php');
}

$controller = new ProductController();
$p = $controller->getProductById($id);
if (!$p) {
    redirect(BASE_URL . '/index.php');
}
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="shop-layout">
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main class="shop-main">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <div class="product-detail">
            <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>"
                 alt="<?php echo htmlspecialchars($p['product_title']); ?>">

            <div class="product-info">
                <h1><?php echo htmlspecialchars($p['product_title']); ?></h1>
                <p class="price">GHS <?php echo number_format($p['product_price'], 2); ?></p>
                <p><strong>Category:</strong>
                    <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo (int) $p['product_cat']; ?>"><?php echo htmlspecialchars($p['cat_name']); ?></a></p>
                <p><strong>Brand:</strong>
                    <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo (int) $p['product_brand']; ?>"><?php echo htmlspecialchars($p['brand_name']); ?></a></p>
                <p><?php echo nl2br(htmlspecialchars($p['product_desc'] ?? '')); ?></p>
                <p><strong>Keywords:</strong> <?php echo htmlspecialchars($p['product_keywords'] ?? ''); ?></p>
                <a class="add-cart big" href="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php?add_cart=<?php echo (int) $p['product_id']; ?>">Add to Cart</a>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
