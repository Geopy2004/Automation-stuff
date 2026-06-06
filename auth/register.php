<?php
include("../config/db.php");
require_once __DIR__ . '/../automation/lib/init-database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($fullname === '' || $username === '' || $password === '') {
    echo "Please complete all required fields. <a href='../register.php'>Go back</a>";
    exit();
}

if ($password !== $confirmPassword) {
    echo "Passwords do not match. <a href='../register.php'>Go back</a>";
    exit();
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullname, $username, $passwordHash);

if ($stmt->execute()) {
    header("Location: ../login.php");
    exit();
} else {
    echo "Error registering user. The username may already exist. <a href='../register.php'>Go back</a>";
}

$stmt->close();
$conn->close();
?>
