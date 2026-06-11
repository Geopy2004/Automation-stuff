<?php
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$authUser = $_SESSION['user'] ?? null;
$nav = [
    'dashboard' => 'Dashboard',
    'email' => 'Email',
    'excel' => 'Excel',
    'word' => 'Word',
];
if (($authUser['role'] ?? '') === 'admin') {
    $nav['users'] = 'Users';
    $nav['logs'] = 'Logs';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? config('app.name')) ?></title>
  <link rel="stylesheet" href="<?= e(app_url('assets/css/app.css')) ?>">
</head>
<body>
<?php if ($authUser): ?>
  <div class="shell">
    <div class="loading-overlay" aria-hidden="true">
      <div class="loader">
        <span class="loader-emblem"></span>
        <span class="loader-text">Loading</span>
        <span class="loader-dots"><i></i><i></i><i></i><i></i><i></i></span>
      </div>
    </div>
    <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="app-sidebar">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <div class="sidebar-backdrop" data-close-sidebar></div>
    <aside class="sidebar" id="app-sidebar">
      <a class="brand" href="<?= e(app_url('dashboard')) ?>">Automation<span>Suite</span></a>
      <nav>
        <?php foreach ($nav as $path => $label): ?>
          <a href="<?= e(app_url($path)) ?>" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', $path) ? 'active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>
      <form method="post" action="<?= e(app_url('logout')) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf ?? '') ?>">
        <button class="logout" type="submit">Logout</button>
      </form>
    </aside>
    <main class="content">
      <header class="topbar">
        <div>
          <p class="eyebrow"><?= e(config('app.name')) ?></p>
          <h1><?= e($title ?? 'Dashboard') ?></h1>
        </div>
        <div class="profile">
          <strong><?= e($authUser['name']) ?></strong>
          <span><?= e(ucfirst($authUser['role'])) ?></span>
        </div>
      </header>
      <?php foreach ($flash as $type => $message): ?>
        <div class="alert <?= e($type) ?>"><?= e($message) ?></div>
      <?php endforeach; ?>
      <?php require $viewFile; ?>
    </main>
  </div>
<?php else: ?>
  <div class="loading-overlay" aria-hidden="true">
    <div class="loader">
      <span class="loader-emblem"></span>
      <span class="loader-text">Loading</span>
      <span class="loader-dots"><i></i><i></i><i></i><i></i><i></i></span>
    </div>
  </div>
  <main class="auth-page">
    <?php foreach ($flash as $type => $message): ?>
      <div class="alert <?= e($type) ?>"><?= e($message) ?></div>
    <?php endforeach; ?>
    <?php require $viewFile; ?>
  </main>
<?php endif; ?>
<script src="<?= e(app_url('assets/js/app.js')) ?>"></script>
</body>
</html>
