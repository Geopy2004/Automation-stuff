<section class="card">
  <div class="card-head">
    <div><h2>XLSX Export</h2><p>Generate an Excel workbook from recent activity logs.</p></div>
    <form method="post" action="<?= e(app_url('excel/export')) ?>">
      <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
      <button class="btn primary" type="submit">Export XLSX</button>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Created</th><th>Download</th></tr></thead>
      <tbody>
      <?php foreach ($files as $file): ?>
        <tr><td><?= e($file['title']) ?></td><td><?= e($file['created_at']) ?></td><td><a href="<?= e('../' . $file['file_path']) ?>" download>Download</a></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
