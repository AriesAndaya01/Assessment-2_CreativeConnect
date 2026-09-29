<?php
require_once __DIR__ . '/includes/db.php';
log_action('logout');
$_SESSION = []; session_destroy();
header('Location: login.php');
