<?php
require_once __DIR__ . '/includes/db.php';
$u = require_login(['admin']); $roles = ['admin', 'staff', 'client'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok()) {
    $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0); $self = $id === (int)$u['id'];
    try {
        if ($act === 'delete' && !$self) {
            db()->prepare('UPDATE requests SET assigned_to = NULL WHERE assigned_to = ?')->execute([$id]);
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]); log_action('user_deleted', "id $id"); flash('User deleted.');
        } elseif ($act === 'role' && !$self && in_array($_POST['role'] ?? '', $roles, true)) {
            db()->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$_POST['role'], $id]); log_action('role_changed', "id $id -> " . $_POST['role']); flash('Role updated.');
        } elseif ($act === 'create') {
            $n = trim($_POST['name'] ?? ''); $em = strtolower(trim($_POST['email'] ?? '')); $pw = $_POST['password'] ?? '';
            if (strlen($n) < 2 || !filter_var($em, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8 || !in_array($_POST['role'] ?? '', $roles, true)) flash('Check the fields (password min 8 chars).');
            else { db()->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)')->execute([$n, $em, password_hash($pw, PASSWORD_DEFAULT), $_POST['role']]); log_action('user_created', $em); flash('User created.'); }
        }
    } catch (PDOException $e) { flash('Could not save (email may already exist).'); }
    header('Location: users.php'); exit;
}
$users = db()->query('SELECT id,name,email,role FROM users ORDER BY FIELD(role,"admin","staff","client"), name')->fetchAll();
dash_top('Users & roles'); ?>
<h1>Users &amp; roles</h1>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead><tbody>
<?php foreach ($users as $x): $self = (int)$x['id'] === (int)$u['id']; ?><tr><td><?= e($x['name']) ?><?= $self ? ' (you)' : '' ?></td><td><?= e($x['email']) ?></td>
<td><?php if ($self): ?><?= e($x['role']) ?><?php else: ?><form method="post" class="inline-form"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="role"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
<select name="role" aria-label="Role"><?php foreach ($roles as $r): ?><option <?= $r === $x['role'] ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select><button class="btn btn-ghost">Save</button></form><?php endif; ?></td>
<td><?php if (!$self): ?><form method="post" onsubmit="return confirm('Delete this user?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>"><button class="btn btn-ghost">Delete</button></form><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<h2 style="margin-top:2rem">Add user</h2>
<form method="post" class="contact-form narrow"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="create">
<div class="form-field"><label for="name">Name</label><input id="name" name="name" required></div>
<div class="form-field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
<div class="form-field"><label for="password">Password (min 8)</label><input id="password" name="password" type="password" minlength="8" required></div>
<div class="form-field"><label for="role">Role</label><select id="role" name="role"><?php foreach ($roles as $r): ?><option><?= $r ?></option><?php endforeach; ?></select></div>
<button class="btn btn-primary" type="submit">Create user</button></form>
<?php dash_bottom();
