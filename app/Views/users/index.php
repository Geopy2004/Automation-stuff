<section class="grid two">
  <article class="card">
    <div class="card-head"><h2>Create User</h2></div>
    <form method="post" action="<?= e(app_url('users/create')) ?>" class="stack">
      <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
      <label>Name<input type="text" name="name" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" minlength="8" required></label>
      <label>Role<select name="role"><option value="user">User</option><option value="admin">Admin</option></select></label>
      <button class="btn primary" type="submit">Create</button>
    </form>
  </article>
  <article class="card">
    <div class="card-head"><h2>Users</h2></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $row): ?>
          <tr>
            <td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['role']) ?></td><td><?= $row['is_active'] ? 'Active' : 'Disabled' ?></td>
            <td>
              <form method="post" action="<?= e(app_url('users/toggle')) ?>">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                <button class="btn ghost" type="submit">Toggle</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>
