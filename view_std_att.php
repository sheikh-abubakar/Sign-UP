<?php
session_start();
include "connect.php"; // Database connection file

// Check if professor is logged in
if (!isset($_SESSION['f_id'])) {
    header('Location: login.php');
    exit();
}

$prof_id = $_SESSION['f_id']; // Logged-in professor's ID
$selected_class = isset($_GET['class_code']) ? $_GET['class_code'] : null;
$date_filter = isset($_GET['att_date']) ? $_GET['att_date'] : date('Y-m-d'); // Default to today's date

$current_date = date('Y-m-d'); // Current system date
$show_take_attendance_button = ($date_filter === $current_date); // Condition for button visibility

// Fetch classes taught by the professor
$query_classes = "SELECT DISTINCT c.CLASS_CODE, cr.CRS_TITLE, c.ROOM_CODE
                  FROM Class c
                  JOIN Course cr ON c.CRS_CODE = cr.CRS_CODE
                  WHERE c.PROF_ID = ?";
$stmt_classes = $conn->prepare($query_classes);
$stmt_classes->bind_param("s", $prof_id);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
$classes = $result_classes->fetch_all(MYSQLI_ASSOC);
$stmt_classes->close();

// Fetch attendance if a class is selected
$attendance_data = [];
if ($selected_class) {
    $query_attendance = "SELECT a.ATTENDANCE_ID, a.CLASS_DATE, a.START_TIME, a.END_TIME,
                                ad.STU_ID, ad.STATUS, p.FNAME, p.LNAME
                         FROM Attendance a
                         JOIN Attendance_details ad ON a.ATTENDANCE_ID = ad.ATTENDANCE_ID
                         JOIN Student s ON ad.STU_ID = s.STU_ID
                         JOIN Person p ON s.STU_ID = p.PERSON_ID
                         WHERE a.CLASS_CODE = ? AND a.CLASS_DATE = ?";
    $stmt_attendance = $conn->prepare($query_attendance);
    $stmt_attendance->bind_param("ss", $selected_class, $date_filter);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();
    $attendance_data = $result_attendance->fetch_all(MYSQLI_ASSOC);
    $stmt_attendance->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>

    <!-- Flatpickr CSS for Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Basic Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        form, table {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .form-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        label, select, input {
            width: 48%;
        }
        button, a.take-attendance {
            display: inline-block;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        a.take-attendance:hover {
            background-color: #0056b3;
        }
        .no-data {
            text-align: center;
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>View Attendance</h2>

    <!-- Form to select class and date -->
    <form method="GET" action="">
        <div class="form-group">
            <label for="class_code">Select Class:</label>
            <select name="class_code" id="class_code" required>
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['CLASS_CODE']); ?>" 
                        <?= ($class['CLASS_CODE'] == $selected_class) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($class['CRS_TITLE']) . " (" . htmlspecialchars($class['ROOM_CODE']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="att_date">Select Date:</label>
            <input type="text" name="att_date" id="att_date" value="<?= htmlspecialchars($date_filter); ?>" required>
        </div>
        <div class="form-group">
            <button type="submit">View Attendance</button>
        </div>
        

    </form>

    <!-- Display Take Attendance Button if Date Matches Current Date -->
   


    <!-- Display attendance data if available -->
    <?php if ($selected_class): ?>
        <h3 style="text-align: center;">Attendance for Class: <?= htmlspecialchars($selected_class); ?> on <?= htmlspecialchars($date_filter); ?></h3>
        <div class="form-group">
    <button onclick="window.location.href='update_std_att.php'" 
            style="padding: 10px 20px; background-color: #007bff; margin-left: 600px;color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; ">
        Take Attendance
    </button>
</div>
        <?php if (!empty($attendance_data)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['STU_ID']); ?></td>
                            <td><?= htmlspecialchars($row['FNAME'] . " " . $row['LNAME']); ?></td>
                            <td><?= htmlspecialchars($row['STATUS']); ?></td>
                            <td><a href="update_std_att.php?attendance_id=<?= htmlspecialchars($row['ATTENDANCE_ID']); ?>&stu_id=<?= htmlspecialchars($row['STU_ID']); ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No attendance records found for this date.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Flatpickr Calendar Script -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#att_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true
        });
    </script>
</body>
</html>
