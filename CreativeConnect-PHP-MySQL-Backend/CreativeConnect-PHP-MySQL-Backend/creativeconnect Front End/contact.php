<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$user = current_user();
$values = [
    'fullName' => (string) ($user['full_name'] ?? ''),
    'email' => (string) ($user['email'] ?? ''),
    'company' => (string) ($user['company'] ?? ''),
    'service' => '',
    'deadline' => '',
    'budget' => '',
    'message' => '',
];
$errors = [];

if (is_post()) {
    require_login();
    verify_csrf();
    [$values, $errors] = validate_project_request($_POST);

    if (!$errors) {
        $statement = db()->prepare(
            'INSERT INTO project_requests
             (user_id, contact_name, contact_email, company, service, preferred_deadline, budget, brief)
             VALUES (:user_id, :contact_name, :contact_email, :company, :service, :deadline, :budget, :brief)'
        );
        $statement->execute([
            'user_id' => current_user()['id'],
            'contact_name' => $values['fullName'],
            'contact_email' => $values['email'],
            'company' => $values['company'],
            'service' => $values['service'],
            'deadline' => $values['deadline'],
            'budget' => $values['budget'],
            'brief' => $values['message'],
        ]);

        set_flash('success', 'Your project request was added successfully.');
        redirect('dashboard.php');
    }
}

$pageTitle = 'CreativeConnect | New Project Request';
$pageDescription = 'Submit a new project request to CreativeConnect.';
$currentPage = 'contact';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="page-hero" aria-labelledby="contact-title">
        <div class="container">
          <span class="detail-mark" aria-hidden="true"><i></i><i></i><i></i></span>
          <p class="eyebrow">Start a project</p>
          <h1 id="contact-title">Start your project.</h1>
        </div>
      </section>

      <section class="section" aria-labelledby="form-title">
        <div class="container contact-grid">
          <article>
            <span class="detail-mark" aria-hidden="true"><i></i><i></i><i></i></span>
            <h2 id="form-title">Submit a project request</h2>
            <p>Registered clients can submit a request and manage it from the dashboard.</p>
            <ul class="contact-points">
              <li>Email: hello@creativeconnect.example</li>
              <li>Phone: +61 400 000 000</li>
              <li>Location: Philippines / Remote</li>
            </ul>
          </article>

          <?php if (!logged_in()): ?>
            <article class="contact-form login-required">
              <h2>Client account required</h2>
              <p>Please log in before submitting a project request.</p>
              <div class="action-row">
                <a class="btn btn-primary" href="login.php?next=contact.php">Log in</a>
                <a class="btn btn-ghost" href="register.php">Create account</a>
              </div>
            </article>
          <?php else: ?>
            <form class="contact-form" action="contact.php" method="post" data-project-request-form novalidate>
              <?= csrf_input() ?>
              <?php if ($errors): ?>
                <div class="alert alert-error" role="alert">Please correct the highlighted fields.</div>
              <?php endif; ?>

              <div class="form-field">
                <label for="full-name">Full name</label>
                <input id="full-name" name="fullName" type="text" required maxlength="100" autocomplete="name" value="<?= e($values['fullName']) ?>" aria-describedby="error-full-name">
                <p class="error-message" id="error-full-name" aria-live="polite"><?= e($errors['fullName'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e($values['email']) ?>" aria-describedby="error-email">
                <p class="error-message" id="error-email" aria-live="polite"><?= e($errors['email'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="company">Company / brand name</label>
                <input id="company" name="company" type="text" required maxlength="120" autocomplete="organization" value="<?= e($values['company']) ?>" aria-describedby="error-company">
                <p class="error-message" id="error-company" aria-live="polite"><?= e($errors['company'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="service">Service needed</label>
                <select id="service" name="service" required aria-describedby="error-service">
                  <option value="">Select a service</option>
                  <?php foreach (service_options() as $value => $label): ?>
                    <option value="<?= e($value) ?>"<?= $values['service'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
                <p class="error-message" id="error-service" aria-live="polite"><?= e($errors['service'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="deadline">Preferred deadline</label>
                <input id="deadline" name="deadline" type="date" min="<?= e(date('Y-m-d')) ?>" required value="<?= e($values['deadline']) ?>" aria-describedby="error-deadline">
                <p class="error-message" id="error-deadline" aria-live="polite"><?= e($errors['deadline'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="budget">Estimated budget (AUD)</label>
                <input id="budget" name="budget" type="number" min="100" max="1000000" step="50" required value="<?= e($values['budget']) ?>" aria-describedby="error-budget">
                <p class="error-message" id="error-budget" aria-live="polite"><?= e($errors['budget'] ?? '') ?></p>
              </div>

              <div class="form-field">
                <label for="message">Project brief</label>
                <textarea id="message" name="message" rows="5" required minlength="20" maxlength="3000" aria-describedby="error-message"><?= e($values['message']) ?></textarea>
                <p class="error-message" id="error-message" aria-live="polite"><?= e($errors['message'] ?? '') ?></p>
              </div>

              <button class="btn btn-primary" type="submit">Submit request</button>
              <p class="form-success" role="status" aria-live="polite"></p>
            </form>
          <?php endif; ?>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
