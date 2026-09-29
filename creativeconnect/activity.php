<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(['admin']);
$rows = db()->query('SELECT l.*, u.email FROM activity_log l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.id DESC LIMIT 100')->fetchAll();
dash_top('Activity log'); ?><h1>Activity log</h1><p class="muted">Last 100 security-relevant events (logins, failed logins, status changes, denied access).</p>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Time</th><th>User</th><th>Action</th><th>Detail</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['email'] ?? 'anonymous') ?></td><td><?= e($r['action']) ?></td><td><?= e($r['detail']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php dash_bottom();
