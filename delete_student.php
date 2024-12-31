<?php
session_start();
include "connect.php"; // Database connection file

// Ensure only admin can access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Delete student from related tables first (e.g., Enroll, Attendance)
        $delete_enroll = "DELETE FROM Enroll WHERE STU_ID = ?";
        $stmt_enroll = $conn->prepare($delete_enroll);
        $stmt_enroll->bind_param("s", $student_id);
        $stmt_enroll->execute();
        $stmt_enroll->close();

        $delete_attendance_details = "DELETE FROM ATTENDANCE_details WHERE STU_ID = ?";
        $stmt_attendance_details = $conn->prepare($delete_attendance_details);
        $stmt_attendance_details->bind_param("s", $student_id);
        $stmt_attendance_details->execute();
        $stmt_attendance_details->close();

        // Finally, delete the student from the `Student` and `Person` tables
        $delete_student = "DELETE FROM Student WHERE STU_ID = ?";
        $stmt_student = $conn->prepare($delete_student);
        $stmt_student->bind_param("s", $student_id);
        $stmt_student->execute();
        $stmt_student->close();

        $delete_person = "DELETE FROM Person WHERE PERSON_ID = ?";
        $stmt_person = $conn->prepare($delete_person);
        $stmt_person->bind_param("s", $student_id);
        $stmt_person->execute();
        $stmt_person->close();

        // Commit transaction
        $conn->commit();

        $success_message = "Student with ID $student_id deleted successfully!";
    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Failed to delete the student: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <link rel="stylesheet" href="css/delete_student.css">
</head>
<body>
    <div class="container">
        <h2>Delete a Student</h2>

        <?php if (isset($success_message)): ?>
            <p class="success"><?= $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="student_id">Enter Student ID:</label>
            <input type="text" name="student_id" id="student_id" placeholder="Enter Student ID" required>
            <button type="submit">Delete Student</button>
        </form>

        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
