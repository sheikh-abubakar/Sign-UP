<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Create the query
    $query = "SELECT username AS username , 'admin' AS role FROM admin WHERE username=? AND password=? UNION 
              SELECT stu_id AS username , 'student' AS role FROM student WHERE stu_id=? AND password=? UNION 
              SELECT prof_id AS username , 'professor' AS role FROM professor WHERE prof_id=? AND password=?";
    
    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("ssssss", $user, $pass, $user, $pass, $user, $pass);
        
        // Execute the statement
        $stmt->execute();
        
        // Store the result
        $stmt->store_result();
        $stmt->bind_result($username, $role);
        
        // Fetch the result
        if ($stmt->fetch()) {
            $_SESSION['role'] = $role;
            $_SESSION['username'] = $username;   // Store username in session
            
            if ($role == 'admin') {
                header('Location: admin_dashboard.php');
            } elseif ($role == 'student') {
                header('Location: student_dashboard.php');
            } elseif ($role == 'professor') {
                header('Location: professor_dashboard.php');
            }
            exit();
        } else {
            echo "<p class='error-message' style='color: red; text-align: center; font-weight: bold;'>Invalid username or password!</p>";
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // If the statement couldn't be prepared, show an error message
        echo "<p class='error-message' style='color: red; text-align: center; font-weight: bold;'>Error preparing statement: " . $conn->error . "</p>";
    }

    // Close the connection
    $conn->close();
}
?>
