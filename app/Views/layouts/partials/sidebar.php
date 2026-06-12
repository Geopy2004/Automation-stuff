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
