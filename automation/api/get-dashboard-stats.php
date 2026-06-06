<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dtr_system");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

require_once __DIR__ . '/../lib/init-database.php';

// Get dashboard statistics
$totalFiles = $conn->query("SELECT COUNT(*) as count FROM file_uploads")->fetch_assoc()['count'];
$totalRecords = $conn->query("SELECT SUM(record_count) as count FROM file_uploads WHERE status = 'completed'")->fetch_assoc()['count'] ?? 0;
$completedFiles = $conn->query("SELECT COUNT(*) as count FROM file_uploads WHERE status = 'completed'")->fetch_assoc()['count'];

$successRate = $totalFiles > 0 ? round(($completedFiles / $totalFiles) * 100) : 0;

echo json_encode([
    'totalFiles' => $totalFiles,
    'totalRecords' => $totalRecords,
    'successRate' => $successRate,
    'completedFiles' => $completedFiles
]);

$conn->close();
?>
