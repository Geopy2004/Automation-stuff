<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dtr_system");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

require_once __DIR__ . '/../lib/init-database.php';

// Get recent uploads
$result = $conn->query("
    SELECT id, filename, file_type, record_count, status, uploaded_at 
    FROM file_uploads 
    ORDER BY uploaded_at DESC 
    LIMIT 10
");

$uploads = [];
while ($row = $result->fetch_assoc()) {
    $uploads[] = [
        'id' => $row['id'],
        'filename' => $row['filename'],
        'file_type' => $row['file_type'],
        'record_count' => $row['record_count'],
        'status' => $row['status'],
        'uploaded_at' => $row['uploaded_at']
    ];
}

echo json_encode(['uploads' => $uploads]);

$conn->close();
?>
