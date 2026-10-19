<?php
require_once __DIR__ . '/../core/core.php';

// Checkout and payment come in a later lab.
$_SESSION['error'] = 'Checkout is not available yet.';
redirect(BASE_URL . '/views/cart.php');
