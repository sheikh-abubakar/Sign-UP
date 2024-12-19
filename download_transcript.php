<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header('Location: login.html');
    exit();
}

require 'connect.php';
require 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$stu_id = $_SESSION['username'];

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

// Initialize DOMPDF
$dompdf = new Dompdf();
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Transcript</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; color: purple; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .personal-info th { background-color: purple; color: white; }
    </style>
</head>
<body>
    <h2>Student Transcript</h2>
    <table class="personal-info">
        <tr><th>Name</th><td>' . htmlspecialchars($fname . ' ' . $lname) . '</td></tr>
        <tr><th>Email</th><td>' . htmlspecialchars($email) . '</td></tr>
        <tr><th>Date of Birth</th><td>' . htmlspecialchars($dob) . '</td></tr>
        <tr><th>Gender</th><td>' . htmlspecialchars($gender) . '</td></tr>
        <tr><th>Contact No</th><td>' . htmlspecialchars($contactno) . '</td></tr>
        <tr><th>State Code</th><td>' . htmlspecialchars($state_code) . '</td></tr>
        <tr><th>City Code</th><td>' . htmlspecialchars($city_code) . '</td></tr>
        <tr><th>Postal Code</th><td>' . htmlspecialchars($postal_code) . '</td></tr>
        <tr><th>Degree</th><td>' . htmlspecialchars($degree) . '</td></tr>
        <tr><th>Professor ID</th><td>' . htmlspecialchars($prof_id) . '</td></tr>
        <tr><th>Department Code</th><td>' . htmlspecialchars($dept_code) . '</td></tr>
    </table>
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
        <tbody>';
        foreach ($classes as $class) {
            $html .= '
            <tr>
                <td>' . htmlspecialchars($class['CRS_CODE']) . '</td>
                <td>' . htmlspecialchars($class['CLASS_CODE']) . '</td>
                <td>' . htmlspecialchars($class['ENROLL_DATE']) . '</td>
                <td>' . htmlspecialchars($class['ENROLL_GRADE']) . '</td>
                <td>' . htmlspecialchars($class['PROF_ID']) . '</td>
            </tr>';
        }

$html .= '
        </tbody>
    </table>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('transcript.pdf', ['Attachment' => 1]);  // Force download
?>
