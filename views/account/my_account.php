<?php require_once __DIR__ . '/../../core/core.php';
require_login();
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="form-page">
    <h1>My Account</h1>
    <p>Name: <?php echo htmlspecialchars($_SESSION['customer_name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
