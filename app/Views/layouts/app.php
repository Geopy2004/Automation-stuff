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
$layoutPath = BASE_PATH . '/app/Views/layouts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? config('app.name')) ?></title>
  <link rel="stylesheet" href="<?= e(app_url('assets/css/app.css')) ?>">
  <link rel="stylesheet" href="<?= e(app_url('assets/css/loading.css')) ?>">
</head>
<body>
<?php if ($authUser): ?>
  <div class="shell">
    <?php require $layoutPath . '/partials/loading.php'; ?>
    <?php require $layoutPath . '/partials/sidebar.php'; ?>
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
  <?php require $layoutPath . '/partials/loading.php'; ?>
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
