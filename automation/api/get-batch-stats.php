<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dtr_system");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

require_once __DIR__ . '/../lib/init-database.php';

// Get batch processing statistics
$pendingFiles = $conn->query("SELECT COUNT(*) as count FROM file_uploads WHERE status = 'pending'")->fetch_assoc()['count'];
$processedCount = $conn->query("SELECT COUNT(*) as count FROM file_uploads WHERE status = 'completed'")->fetch_assoc()['count'];
$pendingValidation = $conn->query("SELECT COUNT(*) as count FROM extracted_data WHERE is_valid = false")->fetch_assoc()['count'];

// Get old files (older than 30 days)
$oldFiles = $conn->query("
    SELECT COUNT(*) as count FROM file_uploads 
    WHERE uploaded_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetch_assoc()['count'];

echo json_encode([
    'pendingFiles' => $pendingFiles,
    'processedCount' => $processedCount,
    'pendingValidation' => $pendingValidation,
    'oldFiles' => $oldFiles
]);

$conn->close();
?>
