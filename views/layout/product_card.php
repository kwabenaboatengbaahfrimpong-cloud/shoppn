<?php // one product card. Expects $p (a product row) to be set by the page that includes it. ?>
<div class="product-card">
    <a href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo (int) $p['product_id']; ?>">
        <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>"
             alt="<?php echo htmlspecialchars($p['product_title']); ?>">
    </a>
    <h3><?php echo htmlspecialchars($p['product_title']); ?></h3>
    <p class="price">GHS <?php echo number_format($p['product_price'], 2); ?></p>
    <div class="card-links">
        <a href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo (int) $p['product_id']; ?>">Details</a>
        <a class="add-cart" href="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php?add_cart=<?php echo (int) $p['product_id']; ?>">Add to Cart</a>
    </div>
</div>
