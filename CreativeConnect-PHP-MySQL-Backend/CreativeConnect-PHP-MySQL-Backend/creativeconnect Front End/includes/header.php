<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'CreativeConnect';
$pageDescription = $pageDescription ?? 'CreativeConnect client project management platform.';
$currentPage = $currentPage ?? '';
$user = current_user();
$flash = take_flash();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription) ?>">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" href="assets/icons/Frame.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">
  </head>
  <body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <div class="flow-line" aria-hidden="true">
      <svg class="flow-line-svg" data-flow-svg>
        <path class="flow-line-track" data-flow-track fill="none"></path>
        <path class="flow-line-fill" data-flow-fill fill="none"></path>
        <circle class="flow-line-dot" data-flow-dot r="4"></circle>
      </svg>
    </div>
    <header class="site-header">
      <div class="container nav-wrap">
        <a class="brand" href="index.php" aria-label="CreativeConnect home">
          <span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i></span>
          CreativeConnect
        </a>
        <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Open navigation menu">
          <span></span><span></span><span></span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="Primary">
          <ul>
            <li><a href="index.php"<?= $currentPage === 'home' ? ' aria-current="page"' : '' ?>>Home</a></li>
            <li><a href="index.php#portfolio">Portfolio</a></li>
            <li><a href="services.php"<?= $currentPage === 'services' ? ' aria-current="page"' : '' ?>>Services</a></li>
            <li><a href="contact.php"<?= $currentPage === 'contact' ? ' aria-current="page"' : '' ?>>New Request</a></li>
            <?php if ($user): ?>
              <li><a href="dashboard.php"<?= $currentPage === 'dashboard' ? ' aria-current="page"' : '' ?>>Dashboard</a></li>
              <li>
                <form class="nav-logout" action="logout.php" method="post">
                  <?= csrf_input() ?>
                  <button class="nav-link-button" type="submit">Logout</button>
                </form>
              </li>
            <?php else: ?>
              <li><a href="register.php"<?= $currentPage === 'register' ? ' aria-current="page"' : '' ?>>Register</a></li>
              <li><a class="btn btn-nav" href="login.php"<?= $currentPage === 'login' ? ' aria-current="page"' : '' ?>>Client Login</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
    </header>
    <?php if ($flash): ?>
      <div class="container alert alert-<?= e($flash['type']) ?>" role="status">
        <?= e($flash['message']) ?>
      </div>
    <?php endif; ?>
