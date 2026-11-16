<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Guests can fill a cart, but paying needs an account (orders need a customer_id).
if (!is_logged_in()) {
    $_SESSION['error'] = 'Log in or register to check out. Your cart is kept.';
    redirect(BASE_URL . '/views/login.php');
}

$cart = new CartController();
$summary_items = $cart->getCartItems(get_ip());
$summary_total = $cart->getCartTotal(get_ip());

if (empty($summary_items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(BASE_URL . '/views/cart.php');
}
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<main class="cart-page">
    <h1>Checkout</h1>

    <?php require __DIR__ . '/layout/flash.php'; ?>

    <section class="checkout-box">
        <h2>Your details</h2>
        <p>Name: <?php echo htmlspecialchars($_SESSION['customer_name']); ?></p>
        <p>Email: <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>
        <p class="muted">Your receipt goes to this email.</p>
    </section>

    <h2>Order summary</h2>
    <?php require __DIR__ . '/layout/order_summary.php'; ?>

    <div class="cart-buttons">
        <a class="btn-link" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
        <a class="btn-link primary" href="<?php echo BASE_URL; ?>/views/payment.php">Continue to Payment</a>
    </div>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>
