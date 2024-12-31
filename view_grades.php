<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header('Location: login.html');
    exit();
}

include 'connect.php';

$class_code = $_GET['class_code'];
$stu_id = $_SESSION['username'];  // Use stu_id instead of username

// Fetch grades
$query = "SELECT e.enroll_date,e.enroll_grade FROM enroll e
          WHERE e.CLASS_CODE = ? AND e.STU_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $class_code, $stu_id);
$stmt->execute();
$stmt->bind_result($date, $grade);
$grades = [];
while ($stmt->fetch()) {
    $grades[] = ['Enroll DATE' => $date, 'GRADE' => $grade];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <link rel="stylesheet" href="view_grades_styles.css">
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
        <h1>Grades</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($stu_id); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Enroll Date</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['Enroll DATE']); ?></td>
                        <td><?php echo htmlspecialchars($grade['GRADE']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
