<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header('Location: login.html');
    exit();
}

include 'connect.php';

$class_code = $_GET['class_code'];
$stu_id = $_SESSION['username'];  // Use stu_id instead of username

// Fetch attendance records
$query = "SELECT a.CLASS_DATE, a.START_TIME, a.END_TIME, ad.STATUS FROM ATTENDANCE a
          JOIN ATTENDANCE_DETAILS ad ON a.ATTENDANCE_ID = ad.ATTENDANCE_ID
          WHERE a.CLASS_CODE = ? AND ad.STU_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $class_code, $stu_id);
$stmt->execute();
$stmt->bind_result($class_date, $start_time, $end_time, $status);

$attendance_records = [];
while ($stmt->fetch()) {
    $attendance_records[] = ['CLASS_DATE' => $class_date, 'START_TIME' => $start_time, 'END_TIME' => $end_time, 'STATUS' => $status];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="view_attendance_styles.css">
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
        <h1>Attendance Records</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($stu_id); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['CLASS_DATE']); ?></td>
                        <td><?php echo htmlspecialchars($record['START_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($record['END_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($record['STATUS']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
