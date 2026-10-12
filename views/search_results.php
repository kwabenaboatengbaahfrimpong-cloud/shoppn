<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$query = trim($_GET['user_query'] ?? '');

$controller = new ProductController();
$products = $query === '' ? [] : $controller->searchProducts($query);
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<div class="shop-layout">
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main class="shop-main">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <h1>Search results</h1>

        <?php if ($query === ''): ?>
            <p class="no-results">Type something in the search box to find products.</p>
        <?php else: ?>
            <p class="result-count">
                <?php echo count($products); ?> result(s) for "<?php echo htmlspecialchars($query); ?>"
            </p>

            <?php if (empty($products)): ?>
                <p class="no-results">No products found.</p>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $p): ?>
                        <?php require __DIR__ . '/layout/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
