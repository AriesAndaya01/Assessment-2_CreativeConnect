<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_admin();

if (!is_post()) {
    http_response_code(405);
    header('Allow: POST');
    exit('Status updates must use POST.');
}

verify_csrf();
$id = request_id_from_input($_POST['id'] ?? null);
$status = trim((string) ($_POST['status'] ?? ''));
if (!array_key_exists($status, status_options())) {
    http_response_code(422);
    exit('Invalid project status.');
}

$statement = db()->prepare('UPDATE project_requests SET status = :status WHERE id = :id');
$statement->execute(['status' => $status, 'id' => $id]);

if ($statement->rowCount() === 0 && !find_request(db(), $id)) {
    http_response_code(404);
    exit('Project request not found.');
}

set_flash('success', 'Project status updated.');
redirect('dashboard.php');
