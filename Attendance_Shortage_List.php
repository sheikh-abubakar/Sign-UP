<?php

$host="localhost";
$user="root";
$pass="";
$db="uni";
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}

// Fetch attendance shortage list query
$query = "
    SELECT s.STU_ID, 
           p.FNAME,
           cs.CRS_CODE, 
           COUNT(a.STATUS) AS Total_Classes,
           SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) AS Classes_Attended,
           SUM(CASE WHEN a.STATUS = 'A' THEN 1 ELSE 0 END) AS Classes_Missed,
           ROUND(SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) / COUNT(a.STATUS) * 100, 2) AS Attendance_Percentage,
           CASE 
               WHEN (SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) / COUNT(a.STATUS)) * 100 < 75 
               THEN 'Drop due to attendance shortage' 
               ELSE 'Continue'
           END AS Status
    FROM person p 
    JOIN student s ON p.PERSON_ID = s.STU_ID
    JOIN attendance_details a ON s.STU_ID = a.STU_ID 
    JOIN attendance att ON a.ATTENDANCE_ID = att.ATTENDANCE_ID
    JOIN class c ON att.CLASS_CODE = c.CLASS_CODE 
    JOIN course cs ON c.CRS_CODE = cs.CRS_CODE
    GROUP BY s.STU_ID, cs.CRS_CODE
    HAVING Attendance_Percentage < 75
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Shortage List</title>
    <style>
       
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            padding: 20px;
            background-color: #0073e6;
            color: white;
            margin: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            text-align: left;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0073e6;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .status-drop {
            color: red;
            font-weight: bold;
        }

        .status-continue {
            color: green;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #0073e6;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .download-btn {
            align-items: center;
            margin-left: 500px;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0073e6;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 15px;
            cursor: pointer;
        }
        .download-btn:hover {
            background-color: black;
            color: white;

        }
    </style>
    <!-- Favicon links -->
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="favicons/favicon.svg" />
    <link rel="shortcut icon" href="favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="CMS" />
    <link rel="manifest" href="favicons/site.webmanifest" />
</head>
<body>

<h1>Attendance Shortage List</h1>

<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Course Code</th>
        <th>Total Classes</th>
        <th>Classes Attended</th>
        <th>Classes Missed</th>
        <th>Attendance Percentage</th>
        <th>Status</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['STU_ID']}</td>
                    <td>{$row['FNAME']}</td>
                    <td>{$row['CRS_CODE']}</td>
                    <td>{$row['Total_Classes']}</td>
                    <td>{$row['Classes_Attended']}</td>
                    <td>{$row['Classes_Missed']}</td>
                    <td>{$row['Attendance_Percentage']}</td>
                    <td>{$row['Status']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found.</td></tr>";
    }
    ?>
</table>

<!-- Download PDF button -->
<a href="Att_Shortage_List_download.php" class="download-btn">Download PDF</a>

</body>
</html>

<?php
$conn->close();
?>
