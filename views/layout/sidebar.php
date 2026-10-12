<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebar_controller = new ProductController();
$sidebar_categories = $sidebar_controller->getAllCategories();
$sidebar_brands     = $sidebar_controller->getAllBrands();
?>
<aside class="sidebar">
    <h3>Categories</h3>
    <ul>
        <?php foreach ($sidebar_categories as $c): ?>
            <li><a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo (int) $c['cat_id']; ?>"><?php echo htmlspecialchars($c['cat_name']); ?></a></li>
        <?php endforeach; ?>
        <?php if (empty($sidebar_categories)): ?>
            <li class="empty">None yet</li>
        <?php endif; ?>
    </ul>

    <h3>Brands</h3>
    <ul>
        <?php foreach ($sidebar_brands as $b): ?>
            <li><a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo (int) $b['brand_id']; ?>"><?php echo htmlspecialchars($b['brand_name']); ?></a></li>
        <?php endforeach; ?>
        <?php if (empty($sidebar_brands)): ?>
            <li class="empty">None yet</li>
        <?php endif; ?>
    </ul>
</aside>
