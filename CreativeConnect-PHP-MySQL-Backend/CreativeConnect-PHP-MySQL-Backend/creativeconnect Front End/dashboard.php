<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$pdo = db();
if (is_admin()) {
    $statement = $pdo->query(
        'SELECT project_requests.*, users.full_name AS account_name
         FROM project_requests
         JOIN users ON users.id = project_requests.user_id
         ORDER BY project_requests.created_at DESC'
    );
} else {
    $statement = $pdo->prepare(
        'SELECT project_requests.*, users.full_name AS account_name
         FROM project_requests
         JOIN users ON users.id = project_requests.user_id
         WHERE project_requests.user_id = :user_id
         ORDER BY project_requests.created_at DESC'
    );
    $statement->execute(['user_id' => current_user()['id']]);
}
$requests = $statement->fetchAll();

$pageTitle = 'CreativeConnect | Dashboard';
$pageDescription = 'View and manage CreativeConnect project requests.';
$currentPage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="page-hero dashboard-hero" aria-labelledby="dashboard-title">
        <div class="container section-header">
          <div>
            <p class="eyebrow"><?= is_admin() ? 'Administrator portal' : 'Client portal' ?></p>
            <h1 id="dashboard-title"><?= is_admin() ? 'All project requests' : 'Your project requests' ?></h1>
          </div>
          <a class="btn btn-primary" href="contact.php">Add request</a>
        </div>
      </section>

      <section class="section dashboard-section" aria-label="Project requests">
        <div class="container">
          <?php if (!$requests): ?>
            <div class="empty-state">
              <h2>No project requests yet</h2>
              <p><?= is_admin() ? 'No clients have submitted a request.' : 'Add your first request to start a project.' ?></p>
              <?php if (!is_admin()): ?><a class="btn btn-primary" href="contact.php">Add request</a><?php endif; ?>
            </div>
          <?php else: ?>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <?php if (is_admin()): ?><th scope="col">Client</th><?php endif; ?>
                    <th scope="col">Service</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">Budget</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($requests as $request): ?>
                    <tr>
                      <td>#<?= e((string) $request['id']) ?></td>
                      <?php if (is_admin()): ?><td><?= e($request['account_name']) ?></td><?php endif; ?>
                      <td><?= e(service_options()[$request['service']] ?? $request['service']) ?></td>
                      <td><?= e(date('d M Y', strtotime($request['preferred_deadline']))) ?></td>
                      <td>$<?= e(number_format((float) $request['budget'], 2)) ?></td>
                      <td>
                        <?php if (is_admin()): ?>
                          <form class="status-form" action="request_status.php" method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                            <select name="status" aria-label="Status for request <?= e((string) $request['id']) ?>">
                              <?php foreach (status_options() as $value => $label): ?>
                                <option value="<?= e($value) ?>"<?= $request['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                              <?php endforeach; ?>
                            </select>
                            <button class="btn btn-small" type="submit">Save</button>
                          </form>
                        <?php else: ?>
                          <span class="status-badge status-<?= e($request['status']) ?>"><?= e(status_options()[$request['status']] ?? $request['status']) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="table-actions">
                          <a href="request.php?id=<?= e((string) $request['id']) ?>">View</a>
                          <a href="request_edit.php?id=<?= e((string) $request['id']) ?>">Edit</a>
                          <form action="request_delete.php" method="post" onsubmit="return confirm('Delete this project request?');">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id" value="<?= e((string) $request['id']) ?>">
                            <button class="danger-link" type="submit">Delete</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
