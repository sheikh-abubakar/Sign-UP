<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: login.html');
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard_styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <div class="link-section">
            <div class="column">
                <a href="add_class.php" class="dashboard-link">Add a Class</a>
                <a href="view_classes.php" class="dashboard-link">View Classes</a>
                <a href="edit_class.php" class="dashboard-link">Edit a Class</a>
                <a href="delete_class.php" class="dashboard-link">Delete a Class</a>
            </div>
            <div class="column">
                <a href="add_student.php" class="dashboard-link">Add a Student</a>
                <a href="view_students.php" class="dashboard-link">View Students</a>
                <a href="edit_student.php" class="dashboard-link">Edit a Student</a>
                <a href="delete_student.php" class="dashboard-link">Delete a Student</a> 
            </div>
        </div>
    </main>
</body>
</html>
