<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
function db(): PDO {
    static $pdo = null;
    if (!$pdo) $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    return $pdo;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_ok(): bool { return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? ''); }
function page_top(string $title): void { ?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CreativeConnect | <?= e($title) ?></title><link rel="icon" href="assets/icons/Frame.svg"><link rel="stylesheet" href="style/main.css"></head><body>
<header class="site-header"><div class="container nav-wrap"><a class="brand" href="index.html">CreativeConnect</a>
<nav class="site-nav" style="display:block;position:static;border:0;box-shadow:none;background:none;width:auto;padding:0"><ul style="display:flex;gap:.6rem">
<li><a href="index.html">Home</a></li><li><a href="services.html">Services</a></li><li><a href="contact.html">Contact</a></li>
<?php if (!empty($_SESSION['user'])): ?><li><a href="dashboard.php">Dashboard</a></li><li><a href="logout.php">Log out</a></li><?php endif; ?></ul></nav></div></header><main><section class="section"><div class="container">
<?php }
function page_bottom(): void { ?></div></section></main><footer class="site-footer"><div class="container footer-wrap"><p>&copy; 2026 CreativeConnect. ICT312 Advanced Web Information Systems.</p></div></footer></body></html><?php }

const STATUSES = ['New', 'In Progress', 'In Review', 'Delivered'];
function user(): ?array { return $_SESSION['user'] ?? null; }
function flash(?string $m = null): ?string { if ($m !== null) { $_SESSION['flash'] = $m; return null; } $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function badge(string $s): string { return '<span class="badge s-' . strtolower(str_replace(' ', '-', $s)) . '">' . e($s) . '</span>'; }
function log_action(string $a, string $d = ''): void {
    try { db()->prepare('INSERT INTO activity_log (user_id,action,detail) VALUES (?,?,?)')->execute([user()['id'] ?? null, $a, $d]); } catch (Throwable $e) {}
}
// RBAC data scope: admin = everything, staff = assigned to them, client = their own (by email)
function scope(array $u): array {
    return match ($u['role']) {
        'client' => [' WHERE r.email = ?', [$u['email']]],
        'staff' => [' WHERE r.assigned_to = ?', [$u['id']]],
        default => ['', []],
    };
}
function get_request(int $id, array $u): ?array {
    $st = db()->prepare('SELECT r.*, s.name AS staff_name FROM requests r LEFT JOIN users s ON s.id = r.assigned_to WHERE r.id = ?');
    $st->execute([$id]); $r = $st->fetch();
    if (!$r) return null;
    if ($u['role'] === 'client' && $r['email'] !== $u['email']) return null;
    if ($u['role'] === 'staff' && (int)$r['assigned_to'] !== (int)$u['id']) return null;
    return $r;
}
function require_login(array $roles = []): array {
    $u = user();
    if (!$u) { header('Location: login.php'); exit; }
    if ($roles && !in_array($u['role'], $roles, true)) {
        http_response_code(403); log_action('access_denied', basename($_SERVER['SCRIPT_NAME']));
        dash_top('Forbidden'); echo '<h1>403 – Access denied</h1><p>Your role (<b>' . e($u['role']) . '</b>) cannot open this page.</p>'; dash_bottom(); exit;
    }
    return $u;
}
function dash_top(string $title): void {
    $u = user(); $r = $u['role'] ?? ''; $cur = basename($_SERVER['SCRIPT_NAME']);
    $menu = ['dashboard.php' => 'Dashboard', 'requests.php' => $r === 'client' ? 'My projects' : ($r === 'staff' ? 'My assignments' : 'All requests')];
    if ($r === 'client') $menu['new_request.php'] = 'New request';
    if ($r === 'admin') { $menu['users.php'] = 'Users & roles'; $menu['activity.php'] = 'Activity log'; }
    ?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CreativeConnect | <?= e($title) ?></title><link rel="icon" href="assets/icons/Frame.svg"><link rel="stylesheet" href="style/main.css"></head>
<body class="dash-body"><div class="dash"><aside class="dash-side"><a class="brand" href="index.html">CreativeConnect</a>
<p class="role-badge role-<?= e($r) ?>"><?= e(strtoupper($r)) ?></p>
<nav aria-label="Dashboard"><?php foreach ($menu as $href => $label): ?><a href="<?= $href ?>" <?= $cur === $href ? 'aria-current="page"' : '' ?>><?= e($label) ?></a><?php endforeach; ?></nav>
<div class="dash-user"><strong><?= e($u['name'] ?? '') ?></strong><br><small><?= e($u['email'] ?? '') ?></small><br><a href="logout.php">Log out</a></div></aside>
<main class="dash-main" id="main-content"><?php if ($f = flash()): ?><p class="notice" role="status"><?= e($f) ?></p><?php endif; ?>
<?php }
function dash_bottom(): void { ?></main></div></body></html><?php }
