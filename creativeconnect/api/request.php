<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');
function fail(string $m, int $c = 422): never { http_response_code($c); echo json_encode(['ok' => false, 'error' => $m]); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('POST only.', 405);
$d = array_map(fn($v) => trim((string)$v), $_POST);
$services = ['video-editing','web-development','content-creation','graphic-design','it-support','other'];
if (mb_strlen($d['fullName'] ?? '') < 2) fail('Enter your full name.');
if (!filter_var($d['email'] ?? '', FILTER_VALIDATE_EMAIL)) fail('Enter a valid email.');
if (mb_strlen($d['company'] ?? '') < 2) fail('Enter your company or brand name.');
if (!in_array($d['service'] ?? '', $services, true)) fail('Select a service.');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d['deadline'] ?? '') || $d['deadline'] < date('Y-m-d')) fail('Deadline must be today or later.');
if ((int)($d['budget'] ?? 0) < 100) fail('Budget must be at least 100 AUD.');
if (mb_strlen($d['message'] ?? '') < 20) fail('Project brief must be at least 20 characters.');
try {
    $st = db()->prepare('INSERT INTO requests (full_name,email,company,service,deadline,budget,message) VALUES (?,?,?,?,?,?,?)');
    $st->execute([$d['fullName'], strtolower($d['email']), $d['company'], $d['service'], $d['deadline'], (int)$d['budget'], $d['message']]);
    echo json_encode(['ok' => true, 'id' => (int)db()->lastInsertId()]);
} catch (Throwable $e) {
    fail('Database not ready. Run setup.php first.', 500);
}
