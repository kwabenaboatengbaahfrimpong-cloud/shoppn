<?php
// Loaded on every page. Starts the session, sets the timezone,
// pulls in the DB class, and holds the small helper functions
// used across the whole site.

session_start();
date_default_timezone_set('Africa/Accra');

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
        redirect('/shoppn/views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'Admins only.';
        redirect('/shoppn/index.php');
    }
}
