<?php
session_start();
if ($_SESSION['role'] != 'student') {
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
        <h1>Student Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <div class="link-section">
            <div class="column">
                <a href="view_classes.php" class="dashboard-link">View Classes</a>
                <a href="transcript.php" class="dashboard-link">Transcript </a>
                <!-- <a href="schedule_class_session.html" class="dashboard-link">schedule the Class</a>
                <a href="Class_Report.php" class="dashboard-link">Class Report</a> -->
            </div>
            <!-- <div class="column">
                <a href="add_student.html" class="dashboard-link">Add a Student</a>
                <a href="view_students.php" class="dashboard-link">View Students</a>
                <a href="Attendance_Shortage_List.php" class="dashboard-link">View Attendance Shortage List</a>
                <a href="delete_student.php" class="dashboard-link">Delete a Student</a> 
            </div> -->
        </div>
    </main>
</body>
</html>
