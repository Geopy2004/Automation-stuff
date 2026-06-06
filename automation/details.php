<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/lib/init-database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$upload = null;
$records = [];
$logs = [];
$error = '';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if (!$id || $id < 1) {
    $error = 'Invalid automation ID.';
} else {
    $stmt = $conn->prepare(
        'SELECT id, filename, file_path, file_type, file_size, record_count, status,
            processing_type, target_table, notes, error_message, uploaded_at, processed_at, created_by
        FROM file_uploads
        WHERE id = ?'
    );

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $upload = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$upload) {
        $error = 'Automation upload not found.';
    } else {
        $stmt = $conn->prepare(
            'SELECT row_number, raw_data, validated_data, validation_errors, is_valid, imported, created_at
            FROM extracted_data
            WHERE upload_id = ?
            ORDER BY row_number ASC, id ASC
            LIMIT 100'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $stmt = $conn->prepare(
            'SELECT action, details, status, created_at
            FROM processing_log
            WHERE upload_id = ?
            ORDER BY created_at DESC, id DESC'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Automation Details</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="dashboard-container">
  <div class="page-header">
    <div>
      <p class="eyebrow">Automation</p>
      <h1>Details</h1>
      <p class="page-subtitle">Review uploaded file metadata, extracted data, and processing activity.</p>
    </div>
    <div class="header-actions">
      <a href="dashboard.php" class="btn secondary">Back to Dashboard</a>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="error-message"><?= e($error) ?></div>
  <?php else: ?>
    <section class="panel">
      <div class="section-title">
        <h3><?= e($upload['filename']) ?></h3>
        <span class="status-badge <?= e($upload['status']) ?>"><?= e($upload['status']) ?></span>
      </div>
      <div class="table-wrap">
        <table class="table">
          <tbody>
            <tr>
              <th>ID</th>
              <td><?= e((string) $upload['id']) ?></td>
              <th>File Type</th>
              <td><?= e($upload['file_type']) ?></td>
            </tr>
            <tr>
              <th>Records</th>
              <td><?= e((string) $upload['record_count']) ?></td>
              <th>File Size</th>
              <td><?= e(number_format((int) $upload['file_size'])) ?> bytes</td>
            </tr>
            <tr>
              <th>Processing Type</th>
              <td><?= e($upload['processing_type'] ?: 'N/A') ?></td>
              <th>Target Table</th>
              <td><?= e($upload['target_table'] ?: 'N/A') ?></td>
            </tr>
            <tr>
              <th>Uploaded At</th>
              <td><?= e($upload['uploaded_at']) ?></td>
              <th>Processed At</th>
              <td><?= e($upload['processed_at'] ?: 'Not processed') ?></td>
            </tr>
            <tr>
              <th>Created By</th>
              <td><?= e($upload['created_by'] ?: 'N/A') ?></td>
              <th>File Path</th>
              <td><?= e($upload['file_path']) ?></td>
            </tr>
            <?php if (!empty($upload['notes']) || !empty($upload['error_message'])): ?>
              <tr>
                <th>Notes</th>
                <td><?= e($upload['notes'] ?: 'N/A') ?></td>
                <th>Error</th>
                <td><?= e($upload['error_message'] ?: 'N/A') ?></td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel section-gap">
      <div class="section-title">
        <h3>Extracted Records</h3>
      </div>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Row</th>
              <th>Raw Data</th>
              <th>Validated Data</th>
              <th>Valid</th>
              <th>Imported</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($records): ?>
              <?php foreach ($records as $record): ?>
                <tr>
                  <td><?= e((string) $record['row_number']) ?></td>
                  <td><pre><?= e($record['raw_data']) ?></pre></td>
                  <td><pre><?= e($record['validated_data']) ?></pre></td>
                  <td><?= $record['is_valid'] ? 'Yes' : 'No' ?></td>
                  <td><?= $record['imported'] ? 'Yes' : 'No' ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="empty-table-message">No extracted records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel section-gap">
      <div class="section-title">
        <h3>Processing Log</h3>
      </div>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Action</th>
              <th>Status</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($logs): ?>
              <?php foreach ($logs as $log): ?>
                <tr>
                  <td><?= e($log['created_at']) ?></td>
                  <td><?= e($log['action']) ?></td>
                  <td><?= e($log['status']) ?></td>
                  <td><?= e($log['details']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="empty-table-message">No processing log entries found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
</main>

</body>
</html>
