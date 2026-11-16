<?php
require_once __DIR__ . '/../core/core.php';

$message   = $_SESSION['error'] ?? 'The payment could not be completed.';
$reference = $_SESSION['failed_ref'] ?? '';
unset($_SESSION['error'], $_SESSION['failed_ref']);
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<main class="cart-page">
    <h1>Payment not completed</h1>

    <div class="banner-error"><?php echo htmlspecialchars($message); ?></div>

    <?php if ($reference !== ''): ?>
        <p>Reference: <strong><?php echo htmlspecialchars($reference); ?></strong></p>
        <p class="muted">Keep this reference if money left your account.</p>
    <?php endif; ?>

    <p>Your cart is unchanged.</p>

    <div class="cart-buttons">
        <a class="btn-link" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
        <a class="btn-link primary" href="<?php echo BASE_URL; ?>/views/checkout.php">Try Again</a>
    </div>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>
