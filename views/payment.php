<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack.php';
require_once __DIR__ . '/../controllers/CartController.php';

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

// Paystack wants the amount in pesewas (GHS x 100), as a whole number.
$amount_pesewas = (int) round($summary_total * 100);

// A fresh reference for this checkout, remembered in the session.
// process_payment_action.php only accepts references from this list.
// The last 10 are kept, so a second open tab does not break the first one.
$reference = new_payment_reference();
$_SESSION['pay_refs'] = array_slice(array_merge($_SESSION['pay_refs'] ?? [], [$reference]), -10);
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<main class="cart-page">
    <h1>Payment</h1>

    <?php require __DIR__ . '/layout/flash.php'; ?>

    <?php require __DIR__ . '/layout/order_summary.php'; ?>

    <div class="cart-buttons">
        <a class="btn-link" href="<?php echo BASE_URL; ?>/views/checkout.php">Back</a>
        <button type="button" id="pay-button" class="btn-link primary">
            Pay GHS <?php echo number_format($summary_total, 2); ?> with Paystack
        </button>
    </div>
    <p id="pay-message" class="muted" role="status"></p>
</main>

<script src="https://js.paystack.co/v2/inline.js"></script>
<script>
    var payButton  = document.getElementById('pay-button');
    var payMessage = document.getElementById('pay-message');

    payButton.addEventListener('click', function () {
        payButton.disabled = true;
        payMessage.textContent = 'Opening Paystack...';

        var popup = new PaystackPop();
        popup.newTransaction({
            key: <?php echo json_encode(PAYSTACK_PUBLIC_KEY); ?>,
            email: <?php echo json_encode($_SESSION['customer_email']); ?>,
            amount: <?php echo $amount_pesewas; ?>,
            currency: 'GHS',
            reference: <?php echo json_encode($reference); ?>,
            onSuccess: function (transaction) {
                // The browser only reports back. The server checks with Paystack before saving the order.
                payMessage.textContent = 'Payment received, confirming...';
                window.location = <?php echo json_encode(BASE_URL . '/actions/process_payment_action.php?ref='); ?>
                    + encodeURIComponent(transaction.reference);
            },
            onCancel: function () {
                payButton.disabled = false;
                payMessage.textContent = 'Payment cancelled. Click Pay to try again.';
            }
        });
    });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
