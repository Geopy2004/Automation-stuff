<?php
session_start();
include("../config/db.php");

$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    header("Location: ../dashboard/index.php");
} else {
    echo "Invalid login";
}
?>