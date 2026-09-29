<?php require __DIR__ . '/layout/header.php'; ?>

<main class="form-page">
    <h1>Register</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="register-form" action="../actions/register_action.php" method="POST">
        <label>Full Name</label>
        <input type="text" name="name" id="name" required>
        <span class="field-error" id="name-error"></span>

        <label>Email</label>
        <input type="email" name="email" id="email" required>
        <span class="field-error" id="email-error"></span>

        <label>Password</label>
        <input type="password" name="pass" id="pass" required>
        <span class="field-error" id="pass-error"></span>

        <label>Country</label>
        <select name="country" id="country" required>
            <option value="Ghana">Ghana</option>
            <option value="Nigeria">Nigeria</option>
            <option value="Other">Other</option>
        </select>

        <label>City</label>
        <input type="text" name="city" id="city" required>

        <label>Contact Number</label>
        <input type="text" name="contact" id="contact" required>
        <span class="field-error" id="contact-error"></span>

        <button type="submit" id="register-submit">Create Account</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</main>

<script src="../js/validate.js"></script>

<?php require __DIR__ . '/layout/footer.php'; ?>
