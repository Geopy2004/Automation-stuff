<?php
session_start();
include("../config/db.php");
require_once __DIR__ . '/../automation/lib/init-database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $validPassword = password_verify($password, $user['password']);

    // Allows old md5 accounts to login once, then upgrades their password hash.
    if (!$validPassword && md5($password) === $user['password']) {
        $validPassword = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $user['id']);
        $update->execute();
        $update->close();
    }

    if (!$validPassword) {
        echo "Invalid username or password. <a href='../login.php'>Try again</a>";
        exit();
    }

    $_SESSION['user_id'] = $user['id'];
    header("Location: ../automation/dashboard.php");
    exit();
} else {
    echo "Invalid username or password. <a href='../login.php'>Try again</a>";
}

$stmt->close();
$conn->close();
?>
