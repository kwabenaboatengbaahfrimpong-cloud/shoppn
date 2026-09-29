<?php require __DIR__ . '/layout/header.php'; ?>

<main class="form-page">
    <h1>Login</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="login-form" action="../actions/login_action.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" id="email" required>
        <span class="field-error" id="email-error"></span>

        <label>Password</label>
        <input type="password" name="pass" id="pass" required>
        <span class="field-error" id="pass-error"></span>

        <button type="submit" id="login-submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</main>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
