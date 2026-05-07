<?php
include("../config/db.php");

$fullname = $_POST['fullname'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, username, password)
VALUES ('$fullname', '$username', '$password')";

if ($conn->query($sql)) {
    header("Location: ../login.html");
} else {
    echo "Error registering user";
}
?>