<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (logged_in()) {
    redirect('dashboard.php');
}

$email = '';
$error = '';
$next = safe_login_destination($_GET['next'] ?? $_POST['next'] ?? null);

if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $statement = db()->prepare(
            'SELECT id, full_name, email, company, password_hash, role FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'The email address or password is incorrect.';
        } else {
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $rehash = db()->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
                $rehash->execute(['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
            }

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'company' => $user['company'],
                'role' => $user['role'],
            ];

            set_flash('success', 'Welcome back, ' . $user['full_name'] . '.');
            redirect($next);
        }
    }
}

$pageTitle = 'CreativeConnect | Login';
$pageDescription = 'Log in to the CreativeConnect client portal.';
$currentPage = 'login';
require __DIR__ . '/includes/header.php';
?>
    <main id="main-content">
      <section class="section auth-section" aria-labelledby="login-title">
        <div class="container auth-wrap">
          <form class="contact-form auth-form" action="login.php" method="post" novalidate>
            <?= csrf_input() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <p class="eyebrow">Client portal</p>
            <h1 id="login-title">Log in</h1>
            <?php if ($error): ?>
              <div class="alert alert-error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>
            <div class="form-field">
              <label for="email">Email address</label>
              <input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e($email) ?>">
            </div>
            <div class="form-field">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            <button class="btn btn-primary" type="submit">Log in</button>
            <p class="form-helper">Need an account? <a href="register.php">Register here</a>.</p>
          </form>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
