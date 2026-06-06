<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Files - Data Entry Automation</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="form-container">
  <div class="page-header compact">
    <div>
      <p class="eyebrow">New Upload</p>
      <h1>Upload Excel or Word Files</h1>
      <p class="page-subtitle">Extract file data and optionally import it into a database table.</p>
    </div>
  </div>

  <form id="uploadForm" enctype="multipart/form-data">
    <div class="form-group">
      <label for="fileInput">Select Files</label>
      <div class="file-input-wrapper">
        <label for="fileInput" class="file-input-label">Choose Files</label>
        <input type="file" id="fileInput" name="files[]" multiple accept=".xlsx,.xls,.docx,.doc">
        <div class="file-name" id="fileName">No files selected</div>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="processingType">Processing Type</label>
        <select id="processingType" name="processing_type" required>
          <option value="">Select a process</option>
          <option value="extract">Extract Data Only</option>
          <option value="import">Extract & Import to Database</option>
          <option value="validate">Extract & Validate</option>
          <option value="transform">Extract & Transform Data</option>
        </select>
      </div>

      <div class="form-group">
        <label for="targetTable">Target Table</label>
        <select id="targetTable" name="target_table">
          <option value="">Select table</option>
          <option value="employees">Employees</option>
          <option value="sales">Sales Data</option>
          <option value="inventory">Inventory</option>
          <option value="custom">Custom Table</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="notes">Notes</label>
      <textarea id="notes" name="notes" placeholder="Add processing notes if needed"></textarea>
    </div>

    <div id="message"></div>

    <div class="form-actions">
      <button type="submit" class="btn primary">Upload & Process</button>
      <a href="dashboard.php" class="btn secondary">Back to Dashboard</a>
    </div>
  </form>

  <aside class="info-panel">
    <h4>Supported File Formats</h4>
    <ul class="format-list">
      <li><strong>Excel:</strong> .xlsx, .xls</li>
      <li><strong>Word:</strong> .docx, .doc</li>
      <li><strong>Max file size:</strong> 50 MB</li>
    </ul>
  </aside>
</main>

<script>
  const fileInput = document.getElementById('fileInput');
  const fileName = document.getElementById('fileName');

  fileInput.addEventListener('change', function() {
    const files = Array.from(this.files).map(f => f.name).join(', ');
    fileName.textContent = files || 'No files selected';
  });

  document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const message = document.getElementById('message');
    
    try {
      const response = await fetch('api/process-upload.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        message.innerHTML = `<div class="success-message">${result.message}</div>`;
        setTimeout(() => {
          window.location.href = 'dashboard.php';
        }, 2000);
      } else {
        message.innerHTML = `<div class="error-message">${result.message}</div>`;
      }
    } catch (error) {
      message.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
    }
  });
</script>

</body>
</html>
