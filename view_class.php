<?php
session_start();
require "connect.php"; // Include your database configuration

// Check if faculty session exists
if (!isset($_SESSION['f_id'])) {
    header('Location: login.php');
    exit();
}

$fid = $_SESSION['f_id']; // Get the professor ID from session
$funame = $_SESSION['f_uname']; // Professor username
$pageTitle = "View Classes";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/view_class.css">
</head>
<body>

<div class="classes_list">
    <h3>Your Classes</h3>
    <table class="tab_one">
        <tr>
            <th>Class Code</th>
            <th>Course Title</th>
            <th>Course Credits</th>
            <th>Room Code</th>
            <th>Action</th>
        </tr>

        <?php
        // Fetch classes assigned to the professor
        $query = "SELECT DISTINCT c.CLASS_CODE, co.CRS_TITLE, co.CRS_CREDITS, c.ROOM_CODE 
                  FROM Class c
                  JOIN Course co ON c.CRS_CODE = co.CRS_CODE
                  WHERE c.PROF_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $fid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['CLASS_CODE']) . "</td>
                        <td>" . htmlspecialchars($row['CRS_TITLE']) . "</td>
                        <td>" . htmlspecialchars($row['CRS_CREDITS']) . "</td>
                        <td>" . htmlspecialchars($row['ROOM_CODE']) . "</td>
                        <td><a href='view_class_details.php?class_code=" . urlencode($row['CLASS_CODE']) . "'>View Details</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No classes found</td></tr>";
        }

        $stmt->close();
        ?>
    </table>
</div>

</body>
</html>
