<section class="card">
  <div class="card-head"><h2>Activity Logs</h2></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Created</th></tr></thead>
      <tbody>
      <?php foreach ($logs as $log): ?>
        <tr><td><?= e($log['name'] ?? 'System') ?></td><td><?= e($log['action']) ?></td><td><?= e($log['details']) ?></td><td><?= e($log['ip_address']) ?></td><td><?= e($log['created_at']) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
