<?php
session_start();
require "connect.php"; // Include the database connection

// Ensure professor is logged in
if ($_SESSION['role'] != 'professor') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the student ID, class code, and new grade from the form submission
    $stu_id = $_POST['stu_id'];
    $class_code = $_POST['class_code'];
    $new_grade = $_POST['new_grade'];

    // Validate the inputs (ensure that new_grade is not empty and follows your grading format)
    if (empty($new_grade)) {
        echo "Grade cannot be empty.";
        exit();
    }

    // Update the grade in the database using a prepared statement
    $update_query = "UPDATE Enroll SET ENROLL_GRADE = ? WHERE STU_ID = ? AND CLASS_CODE = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sss", $new_grade, $stu_id, $class_code);
    
    if ($stmt->execute()) {
        // Redirect back to the view grade page or show a success message
        echo "Grade updated successfully!";
        header("Location: view_class_grades.php?class_code=" . $class_code); // Redirect back to class grades view page
    } else {
        echo "Error updating grade: " . $stmt->error;
    }

    $stmt->close(); // Close the statement
}

$conn->close(); // Close the connection
?>
