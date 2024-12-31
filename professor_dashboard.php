<?php
session_start();
if ($_SESSION['role'] != 'professor') {
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
    <title>Professor Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard_styles.css">
    <!-- Favicon links -->
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="favicons/favicon.svg" />
    <link rel="shortcut icon" href="favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="CMS" />
    <link rel="manifest" href="favicons/site.webmanifest" />
</head>
<body>
    <header>
        <h1>Professor Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <div class="link-section">
            <div class="column">
                <a href="view_prof_file.php" class="dashboard-link">View Your Profile</a>
                <a href="view_class.php" class="dashboard-link">View Classes</a>
                <a href="view_std_att.php" class="dashboard-link">Attendance</a>
                <!-- <a href="Class_Report.php" class="dashboard-link">Class Report</a> -->
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
