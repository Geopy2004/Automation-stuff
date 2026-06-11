<section class="card">
  <div class="card-head">
    <div><h2>Gmail / IMAP Inbox</h2><p>Uses credentials from `.env`; Gmail requires an app password.</p></div>
    <form method="post" action="<?= e(app_url('email/sync')) ?>">
      <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
      <button class="btn primary" type="submit">Sync Inbox</button>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Subject</th><th>Sender</th><th>Received</th></tr></thead>
      <tbody>
      <?php foreach ($messages as $message): ?>
        <tr><td><?= e($message['subject']) ?></td><td><?= e($message['sender']) ?></td><td><?= e($message['received_at']) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
