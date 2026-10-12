<?php
require_once __DIR__ . '/../core/core.php';

// The cart is built in a later lab. Until then, send the shopper
// back to the page they came from so the link does not 404.
$_SESSION['error'] = 'The cart is not available yet.';
redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php');
