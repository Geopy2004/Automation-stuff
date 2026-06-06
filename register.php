<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - DTR System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="form-container auth-container">
  <div class="page-header compact">
    <div>
      <p class="eyebrow">New Account</p>
      <h1>Create your account</h1>
      <p class="page-subtitle">Create an account for the DTR system.</p>
    </div>
  </div>

  <form action="auth/register.php" method="POST" class="auth-form">
    <div class="form-group">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" placeholder="Enter full name" required>
    </div>

    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Choose username" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Create password" required>
    </div>

    <div class="form-group">
      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
    </div>

    <button type="submit" class="btn primary btn-block">Register</button>
  </form>

  <p class="auth-switch">
    Already have an account? <a href="login.php">Login</a>
  </p>
</main>

</body>
</html>
