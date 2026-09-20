<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (!is_post()) {
    http_response_code(405);
    header('Allow: POST');
    exit('Logout must be submitted using POST.');
}

verify_csrf();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
}

session_destroy();
session_start();
session_regenerate_id(true);
set_flash('success', 'You have been logged out.');
redirect('index.php');
