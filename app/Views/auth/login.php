<section class="auth-card">
  <p class="eyebrow">Secure Access</p>
  <h1>Login</h1>
  <p>Sign in to continue to the automation dashboard.</p>
  <form method="post" action="<?= e(app_url('login')) ?>">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
    <label>Email<input type="email" name="email" required autocomplete="email"></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn primary" type="submit">Login</button>
  </form>
  <p class="switch">No account? <a href="<?= e(app_url('register')) ?>">Register</a></p>
</section>
