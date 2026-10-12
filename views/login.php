<?php require __DIR__ . '/layout/header.php'; ?>

<main class="auth">
    <div class="auth-brand">
        <hr class="accent-rule">
        <p class="mark">Shoppn</p>
        <p>Log in to pick up where you left off.</p>
    </div>

    <div class="auth-form-panel">
        <div class="inner">
            <h1>Welcome back</h1>
            <p class="auth-subtitle">Log in to your account</p>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form id="login-form" action="../actions/login_action.php" method="POST" novalidate>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                    <span class="field-error" id="email-error"></span>
                </div>

                <div class="field">
                    <label for="pass">Password</label>
                    <input type="password" name="pass" id="pass" required>
                    <span class="field-error" id="pass-error"></span>
                </div>

                <button type="submit" id="login-submit">Log in</button>
            </form>

            <p class="auth-switch">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</main>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
