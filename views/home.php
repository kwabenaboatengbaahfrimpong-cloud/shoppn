<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

// ?cat=N or ?brand=N filters the list, otherwise show a random few
$heading = 'Featured products';
if (isset($_GET['cat']) && filter_var($_GET['cat'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByCategory((int) $_GET['cat']);
    $cat = $controller->getCategoryById((int) $_GET['cat']);
    $heading = $cat ? $cat['cat_name'] : 'Category';
} elseif (isset($_GET['brand']) && filter_var($_GET['brand'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByBrand((int) $_GET['brand']);
    $brand = $controller->getBrandById((int) $_GET['brand']);
    $heading = $brand ? $brand['brand_name'] : 'Brand';
} else {
    $products = $controller->getFeaturedProducts();
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

        <h1><?php echo htmlspecialchars($heading); ?></h1>

        <?php if (empty($products)): ?>
            <p class="no-results">No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <?php require __DIR__ . '/layout/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
