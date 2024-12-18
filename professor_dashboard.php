<?php
session_start();
if ($_SESSION['role'] != 'professor') {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
</head>
<body>
    <h1>Welcome to the Professor Dashboard</h1>
    <a href="logout.php">Logout</a>
</body>
</html>
