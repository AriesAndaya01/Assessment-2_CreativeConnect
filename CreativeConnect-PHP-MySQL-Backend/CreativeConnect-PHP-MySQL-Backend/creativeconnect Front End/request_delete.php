<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

if (!is_post()) {
    http_response_code(405);
    header('Allow: POST');
    exit('Delete requests must use POST.');
}

verify_csrf();
$id = request_id_from_input($_POST['id'] ?? null);
$pdo = db();
$request = find_request($pdo, $id);
if (!$request) {
    http_response_code(404);
    exit('Project request not found.');
}
require_request_access($request);

$statement = $pdo->prepare('DELETE FROM project_requests WHERE id = :id');
$statement->execute(['id' => $id]);
set_flash('success', 'Project request deleted.');
redirect('dashboard.php');
