<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header('Location: login.html');
    exit();
}

include 'connect.php';

$stu_id = $_SESSION['username'];  // Use stu_id instead of username

// Fetch student's enrolled classes
$query = "SELECT c.CLASS_CODE, c.CRS_CODE FROM enroll e 
          JOIN CLASS c ON e.CLASS_CODE = c.CLASS_CODE
          WHERE e.Stu_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $stu_id);
$stmt->execute();
$stmt->bind_result($classCode, $courseCode);

$classes = [];
while ($stmt->fetch()) {
    $classes[] = ['CLASS_CODE' => $classCode, 'CRS_CODE' => $courseCode];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classes</title>
    <link rel="stylesheet" href="view_classes_styles.css">
</head>
<body>
    <header>
        <h1>My Classes</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($stu_id); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <div class="link-section">
            <?php foreach ($classes as $class): ?>
                <div class="class-card">
                    <h2><?php echo htmlspecialchars($class['CRS_CODE']); ?></h2>
                    <a href="view_attendance.php?class_code=<?php echo $class['CLASS_CODE']; ?>" class="dashboard-link">View Attendance</a>
                    <a href="view_grades.php?class_code=<?php echo $class['CLASS_CODE']; ?>" class="dashboard-link">View Grades</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
