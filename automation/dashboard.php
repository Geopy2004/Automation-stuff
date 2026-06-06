<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Data Entry Automation</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="dashboard-container">
  <div class="page-header">
    <div>
      <p class="eyebrow">Automation</p>
      <h1>Dashboard</h1>
      <p class="page-subtitle">Track uploaded files, extracted records, and processing results.</p>
    </div>
    <div class="header-actions">
      <a href="upload.php" class="btn primary">Upload File</a>
      <a href="batch-process.php" class="btn secondary">Batch Process</a>
    </div>
  </div>

  <section class="dashboard-stats" aria-label="Dashboard statistics">
    <div class="stat-card">
      <p>Total Files</p>
      <h3 id="totalFiles">0</h3>
    </div>
    <div class="stat-card">
      <p>Records Extracted</p>
      <h3 id="totalRecords">0</h3>
    </div>
    <div class="stat-card">
      <p>Success Rate</p>
      <h3 id="successRate">0%</h3>
    </div>
  </section>

  <section class="panel">
    <div class="section-title">
      <h3>Recent Uploads</h3>
      <a href="../index.php" class="btn btn-small">Home</a>
    </div>
    <div class="table-wrap">
      <table class="table" id="recentUploads">
        <thead>
          <tr>
            <th>Filename</th>
            <th>Type</th>
            <th>Records</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="empty-table-message">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script src="../assets/js/main.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    fetch('api/get-dashboard-stats.php')
      .then(r => r.json())
      .then(data => {
        document.getElementById('totalFiles').textContent = data.totalFiles || 0;
        document.getElementById('totalRecords').textContent = data.totalRecords || 0;
        document.getElementById('successRate').textContent = (data.successRate || 0) + '%';
      });

    fetch('api/get-recent-uploads.php')
      .then(r => r.json())
      .then(data => {
        const tbody = document.querySelector('#recentUploads tbody');
        if (data.uploads && data.uploads.length > 0) {
          tbody.innerHTML = data.uploads.map(u => `
            <tr>
              <td>${u.filename}</td>
              <td>${u.file_type}</td>
              <td>${u.record_count}</td>
              <td><span class="status-badge ${u.status}">${u.status}</span></td>
              <td>${new Date(u.uploaded_at).toLocaleDateString()}</td>
              <td><a href="details.php?id=${u.id}" class="btn btn-small">View</a></td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="empty-table-message">No uploads yet</td></tr>';
        }
      })
      .catch(e => console.error('Error loading uploads:', e));
  });
</script>

</body>
</html>
