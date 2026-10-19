<?php // shows the one-time success / error message set by an action ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <div class="banner-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
