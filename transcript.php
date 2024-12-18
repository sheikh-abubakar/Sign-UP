<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header('Location: login.html');
    exit();
}

include 'connect.php';

$stu_id = $_SESSION['username'];  // Use stu_id instead of username

// Fetch student personal information
$query = "SELECT p.FNAME, p.LNAME, p.EMAIL, p.DOB, p.GENDER, p.CONTACTNO, p.STATE_CODE, p.CITY_CODE, p.POSTAL_CODE, p.IMG, s.DEGREE, s.PROF_ID, s.DEPT_CODE FROM PERSON p 
          JOIN STUDENT s ON p.PERSON_ID = s.STU_ID WHERE s.STU_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $stu_id);
$stmt->execute();
$stmt->bind_result($fname, $lname, $email, $dob, $gender, $contactno, $state_code, $city_code, $postal_code, $img, $degree, $prof_id, $dept_code);
$stmt->fetch();
$stmt->close();

// Fetch student's classes, grades, enroll dates, and professor IDs
$query = "SELECT c.CRS_CODE, c.CLASS_CODE, e.ENROLL_GRADE, e.ENROLL_DATE, c.PROF_ID FROM CLASS c
          JOIN ENROLL e ON c.CLASS_CODE = e.CLASS_CODE
          WHERE e.STU_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $stu_id);
$stmt->execute();
$stmt->bind_result($course_code, $class_code, $enroll_grade, $enroll_date, $prof_id);

$classes = [];
while ($stmt->fetch()) {
    $classes[] = ['CRS_CODE' => $course_code, 'CLASS_CODE' => $class_code, 'ENROLL_GRADE' => $enroll_grade, 'ENROLL_DATE' => $enroll_date, 'PROF_ID' => $prof_id];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <link rel="stylesheet" href="transcript.css">
</head>
<body>
    <header>
        <h1>Student Information</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($stu_id); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>
    <main>
        <div class="student-info">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($img); ?>" alt="Student Picture">
            <h2><?php echo htmlspecialchars($fname . ' ' . $lname); ?></h2>
            <table class="personal-info">
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($email); ?></td>
                </tr>
                <tr>
                    <th>Date of Birth:</th>
                    <td><?php echo htmlspecialchars($dob); ?></td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td><?php echo htmlspecialchars($gender); ?></td>
                </tr>
                <tr>
                    <th>Contact No:</th>
                    <td><?php echo htmlspecialchars($contactno); ?></td>
                </tr>
                <tr>
                    <th>State Code:</th>
                    <td><?php echo htmlspecialchars($state_code); ?></td>
                </tr>
                <tr>
                    <th>City Code:</th>
                    <td><?php echo htmlspecialchars($city_code); ?></td>
                </tr>
                <tr>
                    <th>Postal Code:</th>
                    <td><?php echo htmlspecialchars($postal_code); ?></td>
                </tr>
                <tr>
                    <th>Degree:</th>
                    <td><?php echo htmlspecialchars($degree); ?></td>
                </tr>
                <tr>
                    <th>Professor ID:</th>
                    <td><?php echo htmlspecialchars($prof_id); ?></td>
                </tr>
                <tr>
                    <th>Department Code:</th>
                    <td><?php echo htmlspecialchars($dept_code); ?></td>
                </tr>
            </table>
        </div>
        <div class="transcript-section">
            <h3>Enrolled Courses</h3>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Class Code</th>
                        <th>Enroll Date</th>
                        <th>Grade</th>
                        <th>Professor ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['CRS_CODE']); ?></td>
                            <td><?php echo htmlspecialchars($class['CLASS_CODE']); ?></td>
                            <td><?php echo htmlspecialchars($class['ENROLL_DATE']); ?></td>
                            <td><?php echo htmlspecialchars($class['ENROLL_GRADE']); ?></td>
                            <td><?php echo htmlspecialchars($class['PROF_ID']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
