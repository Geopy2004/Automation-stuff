<section class="auth-card">
  <p class="eyebrow">New Account</p>
  <h1>Register</h1>
  <p>Create a user account. Admins can manage roles after sign in.</p>
  <form method="post" action="<?= e(app_url('register')) ?>">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
    <label>Name<input type="text" name="name" required autocomplete="name"></label>
    <label>Email<input type="email" name="email" required autocomplete="email"></label>
    <label>Password<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
    <button class="btn primary" type="submit">Register</button>
  </form>
  <p class="switch">Already registered? <a href="<?= e(app_url('login')) ?>">Login</a></p>
</section>
