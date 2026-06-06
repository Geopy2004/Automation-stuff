<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dtr_system");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Include file processor
require_once '../lib/FileProcessor.php';

// Get upload directory
$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Initialize database tables
require_once '../lib/init-database.php';

// Process uploaded files
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['files']) || !isset($_POST['processing_type'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $processingType = $_POST['processing_type'];
    $targetTable = $_POST['target_table'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $processor = new FileProcessor($conn, $uploadDir);
    $successCount = 0;
    $errorCount = 0;
    $lastMessage = '';

    // Handle multiple file uploads
    $files = $_FILES['files'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        $file = [
            'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
            'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
            'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']
        ];

        $result = $processor->processUploadedFile($file, $processingType, $targetTable, $notes);
        
        if ($result['success']) {
            $successCount++;
            $lastMessage = $result['message'];
            
            // If import is requested, perform import
            if ($processingType === 'import' && $targetTable) {
                $processor->importToDatabase($result['uploadId'], $targetTable);
            }
        } else {
            $errorCount++;
            $lastMessage = $result['message'];
        }
    }

    $message = "Processed $successCount file(s) successfully";
    if ($errorCount > 0) {
        $message .= " ($errorCount error(s))";
    }

    echo json_encode([
        'success' => $successCount > 0,
        'message' => $message,
        'successful' => $successCount,
        'failed' => $errorCount
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
