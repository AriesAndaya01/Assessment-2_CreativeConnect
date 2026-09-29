<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(); [$w, $a] = scope($u);
$f = $_GET['status'] ?? '';
if (in_array($f, STATUSES, true)) { $w .= ($w ? ' AND' : ' WHERE') . ' r.status = ?'; $a[] = $f; }
$rows = [];
try { $st = db()->prepare("SELECT r.*, s.name AS staff_name FROM requests r LEFT JOIN users s ON s.id = r.assigned_to$w ORDER BY r.created_at DESC"); $st->execute($a); $rows = $st->fetchAll(); } catch (Throwable $e) { flash('Database error.'); }
dash_top('Requests'); ?>
<h1><?= $u['role'] === 'client' ? 'My projects' : ($u['role'] === 'staff' ? 'My assignments' : 'All requests') ?></h1>
<form method="get" class="inline-form" style="margin-bottom:1rem"><label for="status" class="muted">Filter:</label>
<select id="status" name="status" onchange="this.form.submit()"><option value="">All statuses</option>
<?php foreach (STATUSES as $s): ?><option <?= $s === $f ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select><noscript><button class="btn btn-ghost">Go</button></noscript></form>
<?php if (!$rows): ?><p class="notice">No requests found.</p><?php else: ?>
<div class="table-wrap"><table class="data-table"><thead><tr><th>#</th><?php if ($u['role'] !== 'client'): ?><th>Client</th><?php endif; ?><th>Service</th><th>Deadline</th><th>Budget</th><th>Team member</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= (int)$r['id'] ?></td><?php if ($u['role'] !== 'client'): ?><td><?= e($r['full_name']) ?><br><small><?= e($r['company']) ?></small></td><?php endif; ?>
<td><?= e($r['service']) ?></td><td><?= e($r['deadline']) ?></td><td>$<?= number_format($r['budget']) ?></td><td><?= e($r['staff_name'] ?? 'Unassigned') ?></td><td><?= badge($r['status']) ?></td>
<td><a href="request.php?id=<?= (int)$r['id'] ?>">Open</a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; dash_bottom();
