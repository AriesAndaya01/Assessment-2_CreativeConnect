<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(); $id = (int)($_GET['id'] ?? 0); $role = $u['role'];
$r = get_request($id, $u);
if (!$r) { http_response_code(404); dash_top('Not found'); echo '<h1>Request not found</h1><p>It does not exist, or your role cannot view it. <a href="requests.php">Back</a></p>'; dash_bottom(); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $act = $_POST['action'] ?? '';
    if ($act === 'status' && $role !== 'client' && in_array($_POST['status'] ?? '', STATUSES, true)) {
        db()->prepare('UPDATE requests SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]); log_action('status_change', "#$id -> " . $_POST['status']); flash('Status updated.');
    } elseif ($act === 'assign' && $role === 'admin') {
        $sid = (int)($_POST['staff_id'] ?? 0);
        $ok = $sid === 0 || db()->query("SELECT COUNT(*) FROM users WHERE id = $sid AND role = 'staff'")->fetchColumn();
        if ($ok) { db()->prepare('UPDATE requests SET assigned_to = ? WHERE id = ?')->execute([$sid ?: null, $id]); log_action('assign', "#$id -> staff $sid"); flash('Assignment saved.'); }
    } elseif ($act === 'comment') {
        $b = trim($_POST['body'] ?? '');
        if ($b !== '' && mb_strlen($b) <= 1000) { db()->prepare('INSERT INTO comments (request_id,user_id,body) VALUES (?,?,?)')->execute([$id, $u['id'], $b]); log_action('comment', "#$id"); flash('Comment added.'); }
        else flash('Comment must be 1-1000 characters.');
    }
    header("Location: request.php?id=$id"); exit;
}
$staff = $role === 'admin' ? db()->query("SELECT id,name FROM users WHERE role='staff' ORDER BY name")->fetchAll() : [];
$cs = db()->prepare('SELECT c.body, c.created_at, u.name, u.role FROM comments c JOIN users u ON u.id = c.user_id WHERE c.request_id = ? ORDER BY c.created_at'); $cs->execute([$id]); $comments = $cs->fetchAll();
$step = array_search($r['status'], STATUSES, true);
dash_top('Request #' . $id); ?>
<p><a href="requests.php">&larr; Back to list</a></p>
<h1>Request #<?= $id ?> — <?= e($r['company']) ?></h1>
<ol class="stepper" aria-label="Progress"><?php foreach (STATUSES as $i => $s): ?><li class="<?= $i <= $step ? 'done' : '' ?>"><?= e($s) ?></li><?php endforeach; ?></ol>
<div class="dash-cols"><section class="panel"><h2>Details</h2><dl class="details">
<dt>Client</dt><dd><?= e($r['full_name']) ?> (<?= e($r['email']) ?>)</dd><dt>Service</dt><dd><?= e($r['service']) ?></dd>
<dt>Deadline</dt><dd><?= e($r['deadline']) ?></dd><dt>Budget</dt><dd>$<?= number_format($r['budget']) ?> AUD</dd>
<dt>Team member</dt><dd><?= e($r['staff_name'] ?? 'Unassigned') ?></dd><dt>Status</dt><dd><?= badge($r['status']) ?></dd><dt>Brief</dt><dd><?= nl2br(e($r['message'])) ?></dd></dl></section>
<section class="panel"><h2>Manage</h2>
<?php if ($role === 'client'): ?><p class="muted">Only the team can change status. Leave feedback below.</p><?php else: ?>
<form method="post" class="inline-form"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="status">
<select name="status" aria-label="Status"><?php foreach (STATUSES as $s): ?><option <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select><button class="btn btn-ghost">Update status</button></form>
<?php endif; if ($role === 'admin'): ?>
<form method="post" class="inline-form" style="margin-top:.8rem"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="assign">
<select name="staff_id" aria-label="Assign to"><option value="0">Unassigned</option><?php foreach ($staff as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$s['id'] === (int)$r['assigned_to'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select><button class="btn btn-ghost">Assign</button></form><?php endif; ?></section></div>
<section class="panel"><h2>Feedback &amp; comments</h2>
<?php foreach ($comments as $c): ?><div class="comment"><strong><?= e($c['name']) ?></strong> <span class="role-badge role-<?= e($c['role']) ?>"><?= e($c['role']) ?></span> <small class="muted"><?= e($c['created_at']) ?></small><p><?= nl2br(e($c['body'])) ?></p></div><?php endforeach; ?>
<?php if (!$comments): ?><p class="muted">No comments yet.</p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="comment">
<label for="body" class="muted">Add a comment</label><textarea id="body" name="body" rows="3" maxlength="1000" required></textarea><button class="btn btn-primary" style="margin-top:.6rem">Post comment</button></form></section>
<?php dash_bottom();
