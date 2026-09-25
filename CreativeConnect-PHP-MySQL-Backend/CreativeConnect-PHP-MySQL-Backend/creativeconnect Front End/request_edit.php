<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$id = request_id_from_input($_GET['id'] ?? $_POST['id'] ?? null);
$pdo = db();
$request = find_request($pdo, $id);
if (!$request) {
    http_response_code(404);
    exit('Project request not found.');
}
require_request_access($request);

$values = [
    'fullName' => $request['contact_name'],
    'email' => $request['contact_email'],
    'company' => $request['company'],
    'service' => $request['service'],
    'deadline' => $request['preferred_deadline'],
    'budget' => (string) $request['budget'],
    'message' => $request['brief'],
];
$errors = [];

if (is_post()) {
    verify_csrf();
    [$values, $errors] = validate_project_request($_POST);

    if (!$errors) {
        $statement = $pdo->prepare(
            'UPDATE project_requests
             SET contact_name = :contact_name,
                 contact_email = :contact_email,
                 company = :company,
                 service = :service,
                 preferred_deadline = :deadline,
                 budget = :budget,
                 brief = :brief
             WHERE id = :id'
        );
        $statement->execute([
            'contact_name' => $values['fullName'],
            'contact_email' => $values['email'],
            'company' => $values['company'],
            'service' => $values['service'],
            'deadline' => $values['deadline'],
            'budget' => $values['budget'],
            'brief' => $values['message'],
            'id' => $id,
        ]);
        set_flash('success', 'Project request updated successfully.');
        redirect('request.php?id=' . $id);
    }
}

$pageTitle = 'CreativeConnect | Edit Request';
$pageDescription = 'Edit a CreativeConnect project request.';
$currentPage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="page-hero" aria-labelledby="edit-title">
        <div class="container">
          <p class="eyebrow">Project request #<?= e((string) $id) ?></p>
          <h1 id="edit-title">Edit project request</h1>
        </div>
      </section>
      <section class="section form-page-section">
        <div class="container auth-wrap">
          <form class="contact-form wide-form" action="request_edit.php" method="post" novalidate>
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= e((string) $id) ?>">
            <?php if ($errors): ?><div class="alert alert-error" role="alert">Please correct the highlighted fields.</div><?php endif; ?>
            <div class="form-grid">
              <div class="form-field">
                <label for="full-name">Full name</label>
                <input id="full-name" name="fullName" type="text" required maxlength="100" value="<?= e($values['fullName']) ?>">
                <p class="error-message"><?= e($errors['fullName'] ?? '') ?></p>
              </div>
              <div class="form-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" required maxlength="190" value="<?= e($values['email']) ?>">
                <p class="error-message"><?= e($errors['email'] ?? '') ?></p>
              </div>
              <div class="form-field">
                <label for="company">Company / brand name</label>
                <input id="company" name="company" type="text" required maxlength="120" value="<?= e($values['company']) ?>">
                <p class="error-message"><?= e($errors['company'] ?? '') ?></p>
              </div>
              <div class="form-field">
                <label for="service">Service needed</label>
                <select id="service" name="service" required>
                  <?php foreach (service_options() as $value => $label): ?>
                    <option value="<?= e($value) ?>"<?= $values['service'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
                <p class="error-message"><?= e($errors['service'] ?? '') ?></p>
              </div>
              <div class="form-field">
                <label for="deadline">Preferred deadline</label>
                <input id="deadline" name="deadline" type="date" required value="<?= e($values['deadline']) ?>">
                <p class="error-message"><?= e($errors['deadline'] ?? '') ?></p>
              </div>
              <div class="form-field">
                <label for="budget">Estimated budget (AUD)</label>
                <input id="budget" name="budget" type="number" min="100" max="1000000" step="50" required value="<?= e($values['budget']) ?>">
                <p class="error-message"><?= e($errors['budget'] ?? '') ?></p>
              </div>
              <div class="form-field form-field-wide">
                <label for="message">Project brief</label>
                <textarea id="message" name="message" rows="6" required minlength="20" maxlength="3000"><?= e($values['message']) ?></textarea>
                <p class="error-message"><?= e($errors['message'] ?? '') ?></p>
              </div>
            </div>
            <div class="action-row">
              <button class="btn btn-primary" type="submit">Save changes</button>
              <a class="btn btn-ghost" href="request.php?id=<?= e((string) $id) ?>">Cancel</a>
            </div>
          </form>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
