<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Batch Process - Data Entry Automation</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="dashboard-container">
  <div class="page-header">
    <div>
      <p class="eyebrow">Automation</p>
      <h1>Batch Process Files</h1>
      <p class="page-subtitle">Run queued imports, validations, reports, and cleanup jobs.</p>
    </div>
    <div class="header-actions">
      <a href="dashboard.php" class="btn secondary">Dashboard</a>
    </div>
  </div>

  <section class="panel">
    <div class="section-title">
      <h3>Available Batch Jobs</h3>
    </div>
    <div class="table-wrap">
      <table class="table" id="batchJobs">
        <thead>
          <tr>
            <th>Job Name</th>
            <th>Description</th>
            <th>Files in Queue</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Daily Import</td>
            <td>Process all pending Excel files and import to database</td>
            <td id="job1Count">0</td>
            <td><button class="btn success btn-small" onclick="startBatch('daily')">Start</button></td>
          </tr>
          <tr>
            <td>Weekly Report</td>
            <td>Generate consolidated reports from all processed data</td>
            <td id="job2Count">0</td>
            <td><button class="btn success btn-small" onclick="startBatch('weekly')">Start</button></td>
          </tr>
          <tr>
            <td>Data Validation</td>
            <td>Validate all extracted data for accuracy</td>
            <td id="job3Count">0</td>
            <td><button class="btn success btn-small" onclick="startBatch('validate')">Start</button></td>
          </tr>
          <tr>
            <td>Cleanup Old Files</td>
            <td>Archive and clean up processed files older than 30 days</td>
            <td id="job4Count">0</td>
            <td><button class="btn danger btn-small" onclick="startBatch('cleanup')">Start</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel section-gap">
    <div class="section-title">
      <h3>Processing Progress</h3>
    </div>
    <div id="progressContainer" hidden>
      <div class="info-panel flush">
        <p>Processing: <strong id="currentJob"></strong></p>
        <div class="progress-track">
          <div id="progressBar" class="progress-bar"></div>
        </div>
        <p><small id="progressText">0/0 items processed</small></p>
      </div>
    </div>
    <div id="message"></div>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    loadBatchStats();
  });

  function loadBatchStats() {
    fetch('api/get-batch-stats.php')
      .then(r => r.json())
      .then(data => {
        document.getElementById('job1Count').textContent = data.pendingFiles || 0;
        document.getElementById('job2Count').textContent = data.processedCount || 0;
        document.getElementById('job3Count').textContent = data.pendingValidation || 0;
        document.getElementById('job4Count').textContent = data.oldFiles || 0;
      });
  }

  function startBatch(jobType) {
    const progressContainer = document.getElementById('progressContainer');
    const currentJob = document.getElementById('currentJob');
    const message = document.getElementById('message');
    
    progressContainer.hidden = false;
    currentJob.textContent = jobType.toUpperCase();
    message.innerHTML = '';

    fetch('api/batch-process.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({job_type: jobType})
    })
    .then(r => r.json())
    .then(result => {
      if (result.success) {
        message.innerHTML = `<div class="success-message">Batch job completed: ${result.message}</div>`;
        loadBatchStats();
        setTimeout(() => progressContainer.hidden = true, 2000);
      } else {
        message.innerHTML = `<div class="error-message">Error: ${result.message}</div>`;
      }
    })
    .catch(e => {
      message.innerHTML = `<div class="error-message">${e.message}</div>`;
    });
  }
</script>

</body>
</html>
