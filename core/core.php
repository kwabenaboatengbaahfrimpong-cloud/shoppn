<?php
// Loaded on every page. Starts the session, sets the timezone,
// pulls in the DB class, and holds the small helper functions
// used across the whole site.

session_start();
date_default_timezone_set('Africa/Accra');

require_once __DIR__ . '/db_cred.php';   // database login and BASE_URL
require_once __DIR__ . '/db_class.php';

function get_ip() {
    return $_SERVER['REMOTE_ADDR'];
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 1;
}

function require_login() {
    if (!is_logged_in()) {
        redirect(BASE_URL . '/views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'Admins only.';
        redirect(BASE_URL . '/index.php');
    }
}

// Checks an uploaded product image and moves it into images/products/.
// Used by both add_product_action.php and update_product_action.php so the
// rules are written once. The type is read from the file itself (finfo),
// not from $_FILES['type'], because the browser sends that and it can be faked.
function save_product_image($file) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'The image did not upload, try again'];
    }
    if ($file['size'] > $max_size) {
        return ['ok' => false, 'error' => 'Image is too big, the limit is 2 MB'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($file['tmp_name']);
    if (!in_array($type, $allowed)) {
        return ['ok' => false, 'error' => 'Only JPG, PNG, GIF or WEBP images are allowed'];
    }

    // keep letters, numbers, dots, dashes and underscores only
    $clean = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
    $filename = uniqid() . '_' . $clean;

    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../images/products/' . $filename)) {
        return ['ok' => false, 'error' => 'Could not save the image on the server'];
    }

    return ['ok' => true, 'filename' => $filename];
}
