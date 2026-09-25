<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = request_id_from_input($_GET['id'] ?? null);
$request = find_request(db(), $id);
if (!$request) {
    http_response_code(404);
    exit('Project request not found.');
}
require_request_access($request);

$pageTitle = 'CreativeConnect | Request #' . $id;
$pageDescription = 'View CreativeConnect project request details.';
$currentPage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="page-hero" aria-labelledby="request-title">
        <div class="container section-header">
          <div>
            <p class="eyebrow">Project request #<?= e((string) $id) ?></p>
            <h1 id="request-title"><?= e(service_options()[$request['service']] ?? $request['service']) ?></h1>
          </div>
          <span class="status-badge status-<?= e($request['status']) ?>"><?= e(status_options()[$request['status']] ?? $request['status']) ?></span>
        </div>
      </section>
      <section class="section detail-section">
        <div class="container detail-card">
          <dl class="request-details">
            <div><dt>Client</dt><dd><?= e($request['contact_name']) ?></dd></div>
            <div><dt>Email</dt><dd><?= e($request['contact_email']) ?></dd></div>
            <div><dt>Company</dt><dd><?= e($request['company']) ?></dd></div>
            <div><dt>Preferred deadline</dt><dd><?= e(date('d M Y', strtotime($request['preferred_deadline']))) ?></dd></div>
            <div><dt>Estimated budget</dt><dd>$<?= e(number_format((float) $request['budget'], 2)) ?> AUD</dd></div>
            <div><dt>Submitted</dt><dd><?= e(date('d M Y, g:i a', strtotime($request['created_at']))) ?></dd></div>
            <div class="detail-wide"><dt>Project brief</dt><dd><?= nl2br(e($request['brief'])) ?></dd></div>
          </dl>
          <div class="action-row">
            <a class="btn btn-primary" href="request_edit.php?id=<?= e((string) $id) ?>">Edit request</a>
            <a class="btn btn-ghost" href="dashboard.php">Back to dashboard</a>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
