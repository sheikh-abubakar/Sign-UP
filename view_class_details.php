<?php
session_start();
require "connect.php";

// Check if the faculty session exists
if (!isset($_SESSION['f_id'])) {
    header('Location: login.php');
    exit();
}

$fid = $_SESSION['f_id'];

if (!isset($_GET['class_code'])) {
    header('Location: view_class.php');
    exit();
}

$class_code = $_GET['class_code'];
$pageTitle = "Class Details";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/view_class_details.css">
</head>
<body>

<div class="class-details-container">
    <div class="class-header">
        <h2>Class Details for Class Code: <?php echo htmlspecialchars($class_code); ?></h2>
        <a href="view_class_grades.php?class_code=<?php echo urlencode($class_code); ?>">
            <button class="grade-btn">View Class Grades</button>
        </a>
    </div>

    <div class="class-info">
        <?php
        $query = "SELECT co.CRS_TITLE, co.CRS_DESCRIPTION, co.CRS_CREDITS, c.ROOM_CODE 
                  FROM Class c
                  JOIN Course co ON c.CRS_CODE = co.CRS_CODE
                  WHERE c.CLASS_CODE = ? AND c.PROF_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $class_code, $fid);
        $stmt->execute();
        $result = $stmt->get_result();
        $class_data = $result->fetch_assoc();

        if ($class_data) {
            echo "<p><strong>Course Title:</strong> " . htmlspecialchars($class_data['CRS_TITLE']) . "</p>";
            echo "<p><strong>Course Description:</strong> " . htmlspecialchars($class_data['CRS_DESCRIPTION']) . "</p>";
            echo "<p><strong>Credits:</strong> " . htmlspecialchars($class_data['CRS_CREDITS']) . "</p>";
            echo "<p><strong>Room Code:</strong> " . htmlspecialchars($class_data['ROOM_CODE']) . "</p>";
        } else {
            echo "<p>Class details not found.</p>";
        }

        $stmt->close();

        $enroll_query = "SELECT COUNT(*) as total_students FROM Enroll WHERE CLASS_CODE = ?";
        $stmt = $conn->prepare($enroll_query);
        $stmt->bind_param("s", $class_code);
        $stmt->execute();
        $enroll_result = $stmt->get_result();
        $enroll_data = $enroll_result->fetch_assoc();

        echo "<p><strong>Total Students Enrolled:</strong> " . htmlspecialchars($enroll_data['total_students']) . "</p>";
        ?>
    </div>

    <h3>List of Enrolled Students:</h3>
    <table class="students-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Enrollment Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $student_query = "SELECT e.STU_ID, p.FNAME, p.LNAME, p.EMAIL, e.ENROLL_DATE
                          FROM Enroll e
                          JOIN Student s ON e.STU_ID = s.STU_ID
                          JOIN Person p ON s.STU_ID = p.PERSON_ID
                          WHERE e.CLASS_CODE = ?";
        $stmt = $conn->prepare($student_query);
        $stmt->bind_param("s", $class_code);
        $stmt->execute();
        $student_result = $stmt->get_result();

        if ($student_result->num_rows > 0) {
            while ($row = $student_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['STU_ID']) . "</td>
                        <td>" . htmlspecialchars($row['FNAME']) . " " . htmlspecialchars($row['LNAME']) . "</td>
                        <td>" . htmlspecialchars($row['EMAIL']) . "</td>
                        <td>" . htmlspecialchars($row['ENROLL_DATE']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students enrolled in this class yet.</td></tr>";
        }

        $stmt->close();
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
