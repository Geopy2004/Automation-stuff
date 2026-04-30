<?php
include("../auth/session.php");
include("../config/db.php");

$uid = $_SESSION['user_id'];

$conn->query("INSERT INTO logs (user_id, type) VALUES ($uid, 'Time In')");

header("Location: logs.php");
?>