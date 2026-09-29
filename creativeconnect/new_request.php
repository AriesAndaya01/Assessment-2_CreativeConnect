<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(['client']); $err = '';
$services = ['video-editing' => 'Video & Digital Editing', 'web-development' => 'Web Development', 'content-creation' => 'Content Creation', 'graphic-design' => 'Graphic Design', 'it-support' => 'IT Support & Consulting', 'other' => 'Other'];
$v = array_map('trim', array_map('strval', $_POST));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) $err = 'Session expired. Reload and try again.';
    elseif (mb_strlen($v['company'] ?? '') < 2) $err = 'Enter your company or brand name.';
    elseif (!isset($services[$v['service'] ?? ''])) $err = 'Select a service.';
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $v['deadline'] ?? '') || $v['deadline'] < date('Y-m-d')) $err = 'Deadline must be today or later.';
    elseif ((int)($v['budget'] ?? 0) < 100) $err = 'Budget must be at least 100 AUD.';
    elseif (mb_strlen($v['message'] ?? '') < 20) $err = 'Project brief must be at least 20 characters.';
    else {
        db()->prepare('INSERT INTO requests (full_name,email,company,service,deadline,budget,message) VALUES (?,?,?,?,?,?,?)')
            ->execute([$u['name'], $u['email'], $v['company'], $v['service'], $v['deadline'], (int)$v['budget'], $v['message']]);
        $nid = (int)db()->lastInsertId(); log_action('request_created', "#$nid"); flash('Request submitted.'); header("Location: request.php?id=$nid"); exit;
    }
}
dash_top('New request'); ?>
<h1>New project request</h1><?php if ($err): ?><p class="notice err" role="alert"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="contact-form narrow"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="form-field"><label for="company">Company / brand</label><input id="company" name="company" required value="<?= e($v['company'] ?? '') ?>"></div>
<div class="form-field"><label for="service">Service</label><select id="service" name="service" required><option value="">Select</option><?php foreach ($services as $k => $l): ?><option value="<?= $k ?>" <?= ($v['service'] ?? '') === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
<div class="form-field"><label for="deadline">Deadline</label><input id="deadline" name="deadline" type="date" min="<?= date('Y-m-d') ?>" required value="<?= e($v['deadline'] ?? '') ?>"></div>
<div class="form-field"><label for="budget">Budget (AUD)</label><input id="budget" name="budget" type="number" min="100" step="50" required value="<?= e($v['budget'] ?? '') ?>"></div>
<div class="form-field"><label for="message">Project brief</label><textarea id="message" name="message" rows="4" required><?= e($v['message'] ?? '') ?></textarea></div>
<button class="btn btn-primary" type="submit">Submit request</button></form>
<?php dash_bottom();
