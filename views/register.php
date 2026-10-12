<?php require __DIR__ . '/layout/header.php'; ?>

<main class="auth">
    <div class="auth-brand">
        <hr class="accent-rule">
        <p class="mark">Shoppn</p>
        <p>Create an account to save your details and check out faster next time.</p>
    </div>

    <div class="auth-form-panel">
        <div class="inner">
            <h1>Create your account</h1>
            <p class="auth-subtitle">It takes less than a minute.</p>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form id="register-form" action="../actions/register_action.php" method="POST" novalidate>
                <div class="field">
                    <label for="name">Full name</label>
                    <input type="text" name="name" id="name" required>
                    <span class="field-error" id="name-error"></span>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                    <span class="field-error" id="email-error"></span>
                </div>

                <div class="field">
                    <label for="pass">Password</label>
                    <input type="password" name="pass" id="pass" required>
                    <div class="pw-meter" id="pw-meter">
                        <div class="pw-meter-bar"></div>
                        <div class="pw-meter-bar"></div>
                        <div class="pw-meter-bar"></div>
                    </div>
                    <div class="pw-meter-label" id="pw-meter-label">At least 8 characters, with a letter and a number</div>
                    <span class="field-error" id="pass-error"></span>
                </div>

                <div class="field">
                    <label for="country">Country</label>
                    <select name="country" id="country" required>
                        <option value="Ghana">Ghana</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="field">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" maxlength="30" required>
                    <span class="field-error" id="city-error"></span>
                </div>

                <div class="field">
                    <label for="contact">Contact number</label>
                    <input type="text" name="contact" id="contact" maxlength="15" required>
                    <span class="field-error" id="contact-error"></span>
                </div>

                <button type="submit" id="register-submit">Create account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
</main>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
