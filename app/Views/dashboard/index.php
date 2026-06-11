<section class="stats">
  <?php foreach ($stats as $label => $value): ?>
    <article class="card metric">
      <span><?= e(ucfirst($label)) ?></span>
      <strong><?= e($value) ?></strong>
    </article>
  <?php endforeach; ?>
</section>

<section class="grid two">
  <article class="card">
    <div class="card-head"><h2>Recent Files</h2></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Title</th><th>Type</th><th>Created</th><th>File</th></tr></thead>
        <tbody>
        <?php foreach ($files as $file): ?>
          <tr>
            <td><?= e($file['title']) ?></td>
            <td><?= e(strtoupper($file['type'])) ?></td>
            <td><?= e($file['created_at']) ?></td>
            <td><a href="<?= e('../' . $file['file_path']) ?>" download>Download</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
  <article class="card">
    <div class="card-head"><h2>Activity</h2></div>
    <div class="timeline">
      <?php foreach ($logs as $log): ?>
        <div><strong><?= e($log['action']) ?></strong><span><?= e(($log['name'] ?? 'System') . ' - ' . $log['created_at']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </article>
</section>
