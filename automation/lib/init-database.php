<?php
// Database initialization - creates necessary tables for automation system
$createdLocalConnection = !isset($conn) || !($conn instanceof mysqli);
if ($createdLocalConnection) {
    $conn = new mysqli("localhost", "root", "", "dtr_system");
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create migrations table
$sql0 = "CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(150) NOT NULL UNIQUE,
    batch INT NOT NULL DEFAULT 1,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql0)) {
    echo "Error creating migrations table: " . $conn->error;
}

// Create users table
$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sqlUsers)) {
    echo "Error creating users table: " . $conn->error;
}

// Create file uploads table
$sql1 = "CREATE TABLE IF NOT EXISTS file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('excel', 'word') NOT NULL,
    file_size INT,
    record_count INT DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'error') DEFAULT 'pending',
    processing_type VARCHAR(50),
    target_table VARCHAR(100),
    notes TEXT,
    error_message TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    created_by VARCHAR(100)
)";

if (!$conn->query($sql1)) {
    echo "Error creating file_uploads table: " . $conn->error;
}

// Create extracted data table
$sql2 = "CREATE TABLE IF NOT EXISTS extracted_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    upload_id INT NOT NULL,
    row_number INT,
    column_mapping JSON,
    raw_data JSON,
    validated_data JSON,
    validation_errors JSON,
    is_valid BOOLEAN DEFAULT FALSE,
    imported BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (upload_id) REFERENCES file_uploads(id) ON DELETE CASCADE
)";

if (!$conn->query($sql2)) {
    echo "Error creating extracted_data table: " . $conn->error;
}

// Create data imports table
$sql3 = "CREATE TABLE IF NOT EXISTS data_imports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    upload_id INT NOT NULL,
    target_table VARCHAR(100),
    total_records INT,
    imported_records INT,
    failed_records INT,
    import_status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending',
    import_errors JSON,
    imported_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (upload_id) REFERENCES file_uploads(id) ON DELETE CASCADE
)";

if (!$conn->query($sql3)) {
    echo "Error creating data_imports table: " . $conn->error;
}

// Create batch jobs table
$sql4 = "CREATE TABLE IF NOT EXISTS batch_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_name VARCHAR(100),
    job_type VARCHAR(50),
    status ENUM('pending', 'running', 'completed', 'failed') DEFAULT 'pending',
    total_items INT DEFAULT 0,
    processed_items INT DEFAULT 0,
    failed_items INT DEFAULT 0,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    error_log TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql4)) {
    echo "Error creating batch_jobs table: " . $conn->error;
}

// Create processing log table
$sql5 = "CREATE TABLE IF NOT EXISTS processing_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    upload_id INT,
    action VARCHAR(50),
    details TEXT,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (upload_id) REFERENCES file_uploads(id) ON DELETE CASCADE
)";

if (!$conn->query($sql5)) {
    echo "Error creating processing_log table: " . $conn->error;
}

$migrationNames = [
    '0001_create_migrations_table',
    '0002_create_users_table',
    '0003_create_file_uploads_table',
    '0004_create_extracted_data_table',
    '0005_create_data_imports_table',
    '0006_create_batch_jobs_table',
    '0007_create_processing_log_table'
];

foreach ($migrationNames as $migrationName) {
    $safeName = $conn->escape_string($migrationName);
    $conn->query("INSERT IGNORE INTO migrations (migration_name, batch) VALUES ('$safeName', 1)");
}

if ($createdLocalConnection) {
    echo "Database tables initialized successfully!";
    $conn->close();
}
?>
