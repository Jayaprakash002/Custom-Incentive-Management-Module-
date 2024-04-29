<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "incentive_db"; 
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$packages = array();

// Fetch the chosen holiday packages for the employee with all details
$sql_select_packages = "SELECT c.package_id, c.date, h.location, h.destination, h.duration, h.amenities 
                        FROM claim_package c 
                        INNER JOIN holiday_package h ON c.package_id = h.package_id
                        WHERE c.user_id='$user_id'";
$result_packages = $conn->query($sql_select_packages);

if ($result_packages->num_rows > 0) {
    // Loop through each selected package
    while ($row_package = $result_packages->fetch_assoc()) {
        $package = array(
            'package_id' => $row_package['package_id'],
            'date' => $row_package['date'],
            'location' => $row_package['location'],
            'destination' => $row_package['destination'],
            'duration' => $row_package['duration'],
            'amenities' => $row_package['amenities']
        );
        array_push($packages, $package);
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Holiday Packages</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 800px;
            margin: auto;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .message {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>View Holiday Packages</h2>
    <?php if (empty($packages)) { ?>
        <p class="message">You have not chosen any holiday package yet.</p>
    <?php } else { ?>
        <table>
            <tr>
                <th>Package ID</th>
                <th>Location</th>
                <th>Destination</th>
                <th>Duration (days)</th>
                <th>Amenities</th>
                <th>Date of Selection</th>
            </tr>
            <?php foreach ($packages as $package) { ?>
                <tr>
                    <td><?php echo $package['package_id']; ?></td>
                    <td><?php echo $package['location']; ?></td>
                    <td><?php echo $package['destination']; ?></td>
                    <td><?php echo $package['duration']; ?></td>
                    <td><?php echo $package['amenities']; ?></td>
                    <td><?php echo $package['date']; ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
    <button onclick="window.location.href = 'employee_dashboard.php';" class="back-button">Back</button>
</div>

</body>
</html>
