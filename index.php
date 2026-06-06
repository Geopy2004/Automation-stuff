<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$primaryHref = $isLoggedIn ? 'automation/dashboard.php' : 'login.php';
$primaryText = $isLoggedIn ? 'Dashboard' : 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DTR System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="home-container">
  <div class="page-header compact">
    <div>
      <p class="eyebrow">Daily Time Record</p>
      <h1>DTR System</h1>
      <p class="page-subtitle"><?php echo $isLoggedIn ? 'Continue to your dashboard or sign out.' : 'Login or create an account to access the dashboard.'; ?></p>
    </div>
  </div>

  <div class="button-group">
    <a href="<?php echo $primaryHref; ?>" class="btn primary"><?php echo $primaryText; ?></a>
    <?php if ($isLoggedIn): ?>
      <a href="auth/logout.php" class="btn secondary">Logout</a>
    <?php else: ?>
      <a href="register.php" class="btn secondary">Register</a>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
