<?php
session_start();
require "connect.php"; // Include the proper configuration file

// Ensure professor is logged in
if (!isset($_SESSION['f_id'])) {
    header('Location: login.php');
    exit();
}

$fid = $_SESSION['f_id'];
$class_code = $_GET['class_code']; // Get class code from URL

// Fetch class details using prepared statements
$query = "SELECT co.CRS_TITLE, co.CRS_DESCRIPTION 
          FROM Class c 
          JOIN Course co ON c.CRS_CODE = co.CRS_CODE 
          WHERE c.CLASS_CODE = ? AND c.PROF_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $class_code, $fid);
$stmt->execute();
$result = $stmt->get_result();
$class_data = $result->fetch_assoc();

if (!$class_data) {
    echo "Class details not found.";
    exit();
}

// Fetch student details and grades
$student_query = "SELECT e.STU_ID, p.FNAME, p.LNAME, p.EMAIL, e.ENROLL_DATE, e.ENROLL_GRADE
                  FROM Enroll e
                  JOIN Student s ON e.STU_ID = s.STU_ID
                  JOIN Person p ON s.STU_ID = p.PERSON_ID
                  WHERE e.CLASS_CODE = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("s", $class_code);
$stmt->execute();
$student_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class Grades</title>
    <link rel="stylesheet" href="css/view_class_grades.css">
</head>
<body>

<div class="grade-container">
    <h2>Class Grades for <?php echo htmlspecialchars($class_code); ?></h2>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Enrollment Date</th>
                <th>Current Grade</th>
                <th>Update Grade</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Output enrolled students with their current grades
        while ($row = $student_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['STU_ID']) . "</td>
                    <td>" . htmlspecialchars($row['FNAME']) . " " . htmlspecialchars($row['LNAME']) . "</td>
                    <td>" . htmlspecialchars($row['EMAIL']) . "</td>
                    <td>" . htmlspecialchars($row['ENROLL_DATE']) . "</td>
                    <td>" . htmlspecialchars($row['ENROLL_GRADE']) . "</td>
                    <td>
                        <form action='update_grade.php' method='post'>
                            <input type='hidden' name='stu_id' value='" . htmlspecialchars($row['STU_ID']) . "' />
                            <input type='hidden' name='class_code' value='" . htmlspecialchars($class_code) . "' />
                            <input type='text' name='new_grade' value='" . htmlspecialchars($row['ENROLL_GRADE']) . "' />
                            <button type='submit'>Update</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close(); // Close statement
$conn->close(); // Close the connection
?>
