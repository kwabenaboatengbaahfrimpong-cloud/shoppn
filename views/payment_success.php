<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_login();

$order_id = filter_var($_GET['order_id'] ?? '', FILTER_VALIDATE_INT);
if (!$order_id || $order_id < 1) {
    redirect(BASE_URL . '/index.php');
}

$controller = new CartController();
// Passing the customer_id means one shopper cannot open another shopper's invoice.
$order = $controller->getOrderById($order_id, (int) $_SESSION['customer_id']);

if (!$order) {
    $_SESSION['error'] = 'Order not found.';
    redirect(BASE_URL . '/index.php');
}

$summary_items = $order['items'];
$summary_total = $order['amt'];
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<main class="cart-page invoice">
    <div class="banner-success">Payment successful. Thank you for your order.</div>

    <h1>Invoice #<?php echo (int) $order['invoice_no']; ?></h1>

    <section class="checkout-box">
        <p>Order no: <?php echo (int) $order['order_id']; ?></p>
        <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p>Status: <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></p>
        <p>Customer: <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
            (<?php echo htmlspecialchars($_SESSION['customer_email']); ?>)</p>
    </section>

    <?php require __DIR__ . '/layout/order_summary.php'; ?>

    <p class="muted">Paid in <?php echo htmlspecialchars($order['currency'] ?? 'GHS'); ?> through Paystack.
        A copy of this receipt was sent to your email.</p>

    <div class="cart-buttons no-print">
        <button type="button" class="btn-secondary" onclick="window.print()">Print Invoice</button>
        <a class="btn-link primary" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
    </div>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>
