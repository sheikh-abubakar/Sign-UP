<?php
session_start();
if ($_SESSION['role'] != 'professor') {
    header('Location: login.html');
    exit();
}

// Get the professor username (professor ID) from the session
$username = $_SESSION['username'];

// Include the database connection
include 'connect.php';

// Query to fetch professor details using professor ID from the session
$sql = "SELECT p.FNAME, p.LNAME, p.EMAIL, p.DOB, p.GENDER, p.CONTACTNO, p.STATE_CODE, p.CITY_CODE, p.POSTAL_CODE, prof.PROF_EDUCATION 
        FROM PERSON p
        INNER JOIN PROFESSOR prof ON p.PERSON_ID = prof.PROF_ID
        WHERE prof.PROF_ID = ?";

// Prepare and execute the statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the professor data if available
    if ($professor = $result->fetch_assoc()) {
        // Display the profile data in HTML
    } else {
        echo "<p style='color: red; text-align: center;'>No professor found with the given ID.</p>";
    }

    $stmt->close();
} else {
    echo "<p style='color: red; text-align: center;'>Error preparing statement: " . $conn->error . "</p>";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Profile</title>
    <link rel="stylesheet" href="css/view_prof.css"> 
</head>
<body>
    <div class="profile-container">
        <h1>Professor Profile</h1>
        <table class="profile-table">
            <tr>
                <td><strong>Name:</strong></td>
                <td><?php echo htmlspecialchars($professor['FNAME'] . ' ' . $professor['LNAME']); ?></td>
            </tr>
            <tr>
                <td><strong>Username (ID):</strong></td>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            <tr>
                <td><strong>E-mail:</strong></td>
                <td><?php echo htmlspecialchars($professor['EMAIL']); ?></td>
            </tr>
            <tr>
                <td><strong>Birthday:</strong></td>
                <td><?php echo htmlspecialchars($professor['DOB']); ?></td>
            </tr>
            <tr>
                <td><strong>Education:</strong></td>
                <td><?php echo htmlspecialchars($professor['PROF_EDUCATION']); ?></td>
            </tr>
            <tr>
                <td><strong>Contact:</strong></td>
                <td><?php echo htmlspecialchars($professor['CONTACTNO']); ?></td>
            </tr>
            <tr>
                <td><strong>Gender:</strong></td>
                <td><?php echo htmlspecialchars($professor['GENDER']); ?></td>
            </tr>
            <tr>
                <td><strong>State:</strong></td>
                <td><?php echo htmlspecialchars($professor['STATE_CODE']); ?></td>
            </tr>
            <tr>
                <td><strong>City:</strong></td>
                <td><?php echo htmlspecialchars($professor['CITY_CODE']); ?></td>
            </tr>
            <tr>
                <td><strong>Postal Code:</strong></td>
                <td><?php echo htmlspecialchars($professor['POSTAL_CODE']); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
