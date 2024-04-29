<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// Fetch package claims details for all employees
$sql_select_claims = "SELECT c.user_id, u.name, SUM(s.daily_sale) AS total_sales, h.package_id, h.location, h.destination, h.duration, h.amenities, c.date
                      FROM claim_package c
                      INNER JOIN user u ON c.user_id = u.user_id
                      INNER JOIN sales s ON c.user_id = s.user_id
                      INNER JOIN holiday_package h ON c.package_id = h.package_id
                      GROUP BY c.user_id, h.package_id";
$result_claims = $conn->query($sql_select_claims);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Package Claims</title>
    
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
    </style>
</head>
<body>

<div class="container">
    <h2>View Package Claims</h2>
    <?php if ($result_claims->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Total Sales</th>
                <th>Package ID</th>
                <th>Location</th>
                <th>Destination</th>
                <th>Duration (days)</th>
                <th>Amenities</th>
                <th>Claim Date</th>
            </tr>
            <?php while ($row_claim = $result_claims->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row_claim['user_id']; ?></td>
                    <td><?php echo $row_claim['name']; ?></td>
                    <td><?php echo $row_claim['total_sales']; ?></td>
                    <td><?php echo $row_claim['package_id']; ?></td>
                    <td><?php echo $row_claim['location']; ?></td>
                    <td><?php echo $row_claim['destination']; ?></td>
                    <td><?php echo $row_claim['duration']; ?></td>
                    <td><?php echo $row_claim['amenities']; ?></td>
                    <td><?php echo $row_claim['date']; ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No package claims found.</p>
    <?php } ?>
    <button onclick="window.location.href = 'admin_dashboard.php';" class="back-button">Back</button>
</div>

</body>
</html>
