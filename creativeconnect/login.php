<?php
require_once __DIR__ . '/includes/db.php';
$err = '';
if (user()) { header('Location: dashboard.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!csrf_ok()) throw new Exception('Session expired. Reload and try again.');
        $st = db()->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([strtolower(trim($_POST['email'] ?? ''))]);
        $u = $st->fetch();
        if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
            log_action('login', $u['email']); header('Location: dashboard.php'); exit;
        }
        log_action('login_failed', substr(trim($_POST['email'] ?? ''), 0, 100)); $err = 'Incorrect email or password.';
    } catch (Throwable $e) { $err = $e instanceof PDOException ? 'Database not ready. Run setup.php first.' : $e->getMessage(); }
}
page_top('Client Login'); ?>
<div class="narrow"><h1>Client login</h1>
<?php if ($err): ?><p class="notice err" role="alert"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="contact-form"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="form-field"><label for="email">Email</label><input id="email" name="email" type="email" required autocomplete="email"></div>
<div class="form-field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
<button class="btn btn-primary" type="submit">Log in</button></form></div>
<?php page_bottom();
