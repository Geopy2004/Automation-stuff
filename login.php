<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - DTR System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="form-container auth-container">
  <div class="page-header compact">
    <div>
      <p class="eyebrow">Welcome Back</p>
      <h1>Sign in to DTR</h1>
      <p class="page-subtitle">Access your DTR and automation dashboard.</p>
    </div>
  </div>

  <form action="auth/login.php" method="POST" class="auth-form">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter username" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter password" required>
    </div>

    <button type="submit" class="btn primary btn-block">Sign In</button>
  </form>

  <p class="auth-switch">
    No account yet? <a href="register.php">Create one</a>
  </p>
</main>

</body>
</html>
