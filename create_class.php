<?php

include 'connect.php'; // Include the database connection

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_code = $_POST['class_code'];
    $crs_code = $_POST['crs_code'];
    $prof_id = $_POST['prof_id'];
    $room_code = $_POST['room_code'];

    // Check if the class with the same CLASS_CODE already exists
    $check_sql = "SELECT * FROM CLASS WHERE CLASS_CODE = '$class_code'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // If class already exists, display a message
        echo "Class with this Class Code already exists. Please choose a different Class Code.";
    } else {
        // Insert the class into the database if it doesn't exist
        $sql = "INSERT INTO CLASS (CLASS_CODE, CRS_CODE, PROF_ID, ROOM_CODE) VALUES ('$class_code', '$crs_code', '$prof_id', '$room_code')";

        if ($conn->query($sql) === TRUE) {
            echo "New class created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
