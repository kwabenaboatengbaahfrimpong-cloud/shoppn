<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$cart = new CartController();
$items = $cart->getCartItems(get_ip());
$subtotal = $cart->getCartTotal(get_ip());
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<main class="cart-page">
    <h1>Your cart</h1>

    <?php require __DIR__ . '/layout/flash.php'; ?>

    <?php if (empty($items)): ?>
        <p class="no-results">Your cart is empty.</p>
        <a class="btn-link" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
    <?php else: ?>
        <!-- two separate forms; the inputs in the table point to them with the form attribute -->
        <form id="remove-form" action="<?php echo BASE_URL; ?>/actions/remove_from_cart_action.php" method="POST"></form>
        <form id="update-form" action="<?php echo BASE_URL; ?>/actions/update_qty_action.php" method="POST"></form>

        <table class="admin-table cart-table">
            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit price</th>
                    <th>Line total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="remove[]" form="remove-form"
                                   value="<?php echo (int) $item['p_id']; ?>"
                                   aria-label="Remove <?php echo htmlspecialchars($item['product_title']); ?>">
                        </td>
                        <td class="cart-product">
                            <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>"
                                 alt="<?php echo htmlspecialchars($item['product_title']); ?>">
                            <span><?php echo htmlspecialchars($item['product_title']); ?></span>
                        </td>
                        <td>
                            <input type="number" name="qty[]" form="update-form" class="qty-input"
                                   min="1" max="99" value="<?php echo (int) $item['qty']; ?>">
                            <input type="hidden" name="update[]" form="update-form" value="<?php echo (int) $item['p_id']; ?>">
                        </td>
                        <td>GHS <?php echo number_format($item['product_price'], 2); ?></td>
                        <td>GHS <?php echo number_format($item['qty'] * $item['product_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="subtotal-label">Subtotal</td>
                    <td class="subtotal-value">GHS <?php echo number_format($subtotal, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="cart-buttons">
            <button type="submit" form="remove-form" class="btn-secondary">Remove from Cart</button>
            <button type="submit" form="update-form" class="btn-secondary">Update Quantity</button>
            <a class="btn-link" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
            <a class="btn-link primary" href="<?php echo BASE_URL; ?>/views/checkout.php">Checkout</a>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>
