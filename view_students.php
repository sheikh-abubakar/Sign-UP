<?php
include 'connect.php'; // Include the database connection

// Fetch all students
$sql = "SELECT PERSON.PERSON_ID, PERSON.FNAME, PERSON.LNAME, PERSON.EMAIL, PERSON.DOB, PERSON.GENDER, PERSON.CONTACTNO, PERSON.STATE_CODE, PERSON.CITY_CODE, PERSON.POSTAL_CODE, PERSON.IMG, STUDENT.DEGREE, STUDENT.DEPT_CODE 
        FROM PERSON
        INNER JOIN STUDENT ON PERSON.PERSON_ID = STUDENT.STU_ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="view_std.css">
    <!-- Favicon links -->
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="favicons/favicon.svg" />
    <link rel="shortcut icon" href="favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="CMS" />
    <link rel="manifest" href="favicons/site.webmanifest" />
</head>
<body>
    <div class="table-container">
        <h1>Student List</h1>
        <table>
            <thead>
                <tr>
                    <th>Person ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Contact No</th>
                    <th>State Code</th>
                    <th>City Code</th>
                    <th>Postal Code</th>
                    <th>Degree</th>
                    <th>Department Code</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['PERSON_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['FNAME']); ?></td>
                        <td><?php echo htmlspecialchars($row['LNAME']); ?></td>
                        <td><?php echo htmlspecialchars($row['EMAIL']); ?></td>
                        <td><?php echo htmlspecialchars($row['DOB']); ?></td>
                        <td><?php echo htmlspecialchars($row['GENDER']); ?></td>
                        <td><?php echo htmlspecialchars($row['CONTACTNO']); ?></td>
                        <td><?php echo htmlspecialchars($row['STATE_CODE']); ?></td>
                        <td><?php echo htmlspecialchars($row['CITY_CODE']); ?></td>
                        <td><?php echo htmlspecialchars($row['POSTAL_CODE']); ?></td>
                        <td><?php echo htmlspecialchars($row['DEGREE']); ?></td>
                        <td><?php echo htmlspecialchars($row['DEPT_CODE']); ?></td>
                        <td>
                            <?php if (!empty($row['IMG'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['IMG']); ?>" alt="Student Image" width="100" height="100">
                            <?php else: ?>
                                <p>No Image Available</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
