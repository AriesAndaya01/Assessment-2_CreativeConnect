<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (logged_in()) {
    redirect('dashboard.php');
}

$values = ['full_name' => '', 'email' => '', 'company' => ''];
$errors = [];

if (is_post()) {
    verify_csrf();
    $values = [
        'full_name' => trim((string) ($_POST['full_name'] ?? '')),
        'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
        'company' => trim((string) ($_POST['company'] ?? '')),
    ];
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 100) {
        $errors['full_name'] = 'Enter a name between 2 and 100 characters.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 190) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if ($values['company'] !== '' && mb_strlen($values['company']) > 120) {
        $errors['company'] = 'Company name cannot exceed 120 characters.';
    }
    if (strlen($password) < 8 || strlen($password) > 128) {
        $errors['password'] = 'Password must contain 8 to 128 characters.';
    } elseif ($password !== $passwordConfirmation) {
        $errors['password_confirmation'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $existing = db()->prepare('SELECT id FROM users WHERE email = :email');
        $existing->execute(['email' => $values['email']]);

        if ($existing->fetch()) {
            $errors['email'] = 'An account already exists for this email address.';
        } else {
            $statement = db()->prepare(
                'INSERT INTO users (full_name, email, company, password_hash)
                 VALUES (:full_name, :email, :company, :password_hash)'
            );
            $statement->execute([
                'full_name' => $values['full_name'],
                'email' => $values['email'],
                'company' => $values['company'] !== '' ? $values['company'] : null,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            set_flash('success', 'Account created. You can now log in.');
            redirect('login.php');
        }
    }
}

$pageTitle = 'CreativeConnect | Register';
$pageDescription = 'Create a CreativeConnect client account.';
$currentPage = 'register';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="section auth-section" aria-labelledby="register-title">
        <div class="container auth-wrap">
          <form class="contact-form auth-form" action="register.php" method="post" novalidate>
            <?= csrf_input() ?>
            <p class="eyebrow">Client account</p>
            <h1 id="register-title">Create your account</h1>
            <p class="section-sub">Use your account to submit and manage project requests.</p>

            <div class="form-field">
              <label for="full-name">Full name</label>
              <input id="full-name" name="full_name" type="text" required maxlength="100" autocomplete="name" value="<?= e($values['full_name']) ?>">
              <p class="error-message"><?= e($errors['full_name'] ?? '') ?></p>
            </div>
            <div class="form-field">
              <label for="email">Email address</label>
              <input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e($values['email']) ?>">
              <p class="error-message"><?= e($errors['email'] ?? '') ?></p>
            </div>
            <div class="form-field">
              <label for="company">Company / brand name (optional)</label>
              <input id="company" name="company" type="text" maxlength="120" autocomplete="organization" value="<?= e($values['company']) ?>">
              <p class="error-message"><?= e($errors['company'] ?? '') ?></p>
            </div>
            <div class="form-field">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="new-password">
              <p class="error-message"><?= e($errors['password'] ?? '') ?></p>
            </div>
            <div class="form-field">
              <label for="password-confirmation">Confirm password</label>
              <input id="password-confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="128" autocomplete="new-password">
              <p class="error-message"><?= e($errors['password_confirmation'] ?? '') ?></p>
            </div>
            <button class="btn btn-primary" type="submit">Create account</button>
            <p class="form-helper">Already registered? <a href="login.php">Log in</a>.</p>
          </form>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
