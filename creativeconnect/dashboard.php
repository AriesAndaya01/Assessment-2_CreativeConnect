<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(); $role = $u['role']; [$w, $a] = scope($u);
$counts = array_fill_keys(STATUSES, 0); $recent = []; $unassigned = 0; $ucount = [];
try {
    $st = db()->prepare("SELECT r.status, COUNT(*) c FROM requests r$w GROUP BY r.status"); $st->execute($a);
    foreach ($st->fetchAll() as $row) $counts[$row['status']] = (int)$row['c'];
    $st = db()->prepare("SELECT r.*, s.name AS staff_name FROM requests r LEFT JOIN users s ON s.id = r.assigned_to$w ORDER BY r.created_at DESC LIMIT 5"); $st->execute($a);
    $recent = $st->fetchAll();
    if ($role === 'admin') {
        $unassigned = (int)db()->query('SELECT COUNT(*) FROM requests WHERE assigned_to IS NULL')->fetchColumn();
        $ucount = db()->query('SELECT role, COUNT(*) FROM users GROUP BY role')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
} catch (Throwable $e) { flash('Database error. Run setup.php again.'); }
$total = array_sum($counts);
$perms = [
    'admin' => ['View every request', 'Assign requests to staff', 'Update any status', 'Create, delete and re-role users', 'View the activity log'],
    'staff' => ['View only requests assigned to you', 'Update status of your requests', 'Comment / reply to clients', 'No access to users or activity log (403)'],
    'client' => ['View only your own projects', 'Submit new project requests', 'Track status and assigned team member', 'Leave feedback comments'],
][$role];
dash_top('Dashboard'); ?>
<h1><?= $role === 'admin' ? 'Admin dashboard' : ($role === 'staff' ? 'Team dashboard' : 'Client dashboard') ?></h1>
<p class="muted">Welcome back, <?= e($u['name']) ?>.</p>
<div class="stat-grid">
  <div class="stat"><span><?= $total ?></span>Total <?= $role === 'client' ? 'projects' : 'requests' ?></div>
  <?php foreach ($counts as $s => $c): ?><div class="stat"><span><?= $c ?></span><?= badge($s) ?></div><?php endforeach; ?>
  <?php if ($role === 'admin'): ?><div class="stat"><span><?= $unassigned ?></span>Unassigned</div>
  <div class="stat"><span><?= array_sum($ucount) ?></span>Users (<?= (int)($ucount['admin'] ?? 0) ?> admin, <?= (int)($ucount['staff'] ?? 0) ?> staff, <?= (int)($ucount['client'] ?? 0) ?> client)</div><?php endif; ?>
</div>
<div class="dash-cols"><section class="panel"><h2>Status breakdown</h2>
<?php foreach ($counts as $s => $c): $pct = $total ? round($c / $total * 100) : 0; ?>
<div class="bar-row"><span><?= e($s) ?></span><div class="bar"><i style="width:<?= $pct ?>%"></i></div><b><?= $c ?></b></div><?php endforeach; ?></section>
<section class="panel"><h2>Your role can</h2><ul><?php foreach ($perms as $p): ?><li><?= e($p) ?></li><?php endforeach; ?></ul></section></div>
<section class="panel"><h2>Recent <?= $role === 'client' ? 'projects' : 'requests' ?></h2>
<?php if (!$recent): ?><p>Nothing here yet.<?= $role === 'client' ? ' <a href="new_request.php">Submit a request</a>.' : '' ?></p><?php else: ?>
<div class="table-wrap"><table class="data-table"><thead><tr><th>#</th><th>Company</th><th>Service</th><th>Deadline</th><th>Team member</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($recent as $r): ?><tr><td><?= (int)$r['id'] ?></td><td><?= e($r['company']) ?></td><td><?= e($r['service']) ?></td><td><?= e($r['deadline']) ?></td>
<td><?= e($r['staff_name'] ?? 'Unassigned') ?></td><td><?= badge($r['status']) ?></td><td><a href="request.php?id=<?= (int)$r['id'] ?>">Open</a></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<?php dash_bottom();
