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
$selected_date = date("Y-m-d");
$total_sales = 0;
$incentive = 0;
$eligible_for_holiday = false;
$selected_package_id = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_date = $_POST['selected_date'];
}

// Get total sales till the selected date for the employee
$sql_total_sales = "SELECT SUM(daily_sale) AS total_sales FROM sales WHERE user_id='$user_id' AND DATE(date_time) <= '$selected_date'";
$result_total_sales = $conn->query($sql_total_sales);
if ($result_total_sales->num_rows > 0) {
    $row_total_sales = $result_total_sales->fetch_assoc();
    $total_sales = $row_total_sales['total_sales'];
}

// Calculate incentive based on total sales
if ($total_sales >= 50000) {
    $incentive = 5; 
    $eligible_for_holiday = true;
} elseif ($total_sales >= 30000) {
    $incentive = 3.5; 
    //$eligible_for_holiday = true;
} elseif ($total_sales >= 20000) {
    $incentive = 3; 
} elseif ($total_sales >= 10000) {
    $incentive = 1.5; 
}

// Check if eligible for holiday package
if ($eligible_for_holiday) {
    $sql_select_packages = "SELECT * FROM holiday_package";
    $result_packages = $conn->query($sql_select_packages);

    // Check if employee has already chosen a package
    $sql_check_claim_package = "SELECT * FROM claim_package WHERE user_id='$user_id' AND date='$selected_date'";
    $result_claim_package = $conn->query($sql_check_claim_package);
    if ($result_claim_package->num_rows > 0) {
        $eligible_for_holiday = false; // Employee has already chosen a package
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Personal Sales</title>
   
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

        label {
            font-weight: bold;
        }

        select, input[type="date"] {
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: auto;
            background-color: #4caf50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
    <h2>View Personal Sales</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="selected_date">Select Date:</label>
        <input type="date" id="selected_date" name="selected_date" value="<?php echo $selected_date; ?>">
        <input type="submit" value="View Sales">
    </form>

    <h3>Sales Details</h3>
    <p>Total Sales Till Date: $<?php echo $total_sales; ?></p>
    <p>Incentive: <?php echo $incentive; ?>%</p>
    <?php if ($eligible_for_holiday) { ?>
        <h3>Eligible for Holiday Package</h3>
        <p>You are eligible to choose one holiday package:</p>
        <table>
            <tr>
                <th>Package ID</th>
                <th>Location</th>
                <th>Destination</th>
                <th>Duration (night)</th>
                <th>Amenities</th>
                <th>Action</th>
            </tr>
            <?php while ($row_package = $result_packages->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row_package['package_id']; ?></td>
                    <td><?php echo $row_package['location']; ?></td>
                    <td><?php echo $row_package['destination']; ?></td>
                    <td><?php echo $row_package['duration']; ?></td>
                    <td><?php echo $row_package['amenities']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
                            <input type="hidden" name="package_id" value="<?php echo $row_package['package_id']; ?>">
                            <input type="submit" name="choose_package" value="Choose">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
    <?php if (isset($_POST['choose_package'])) {
        $selected_package_id = $_POST['package_id'];
        // Insert claim package details into claim_package table
        $sql_insert_claim_package = "INSERT INTO claim_package (package_id, user_id, name, date) 
                                     VALUES ('$selected_package_id', '$user_id', '{$_SESSION['name']}', '$selected_date')";
        if ($conn->query($sql_insert_claim_package) === TRUE) {
            echo "<p class='message'>Holiday package chosen successfully. You chose the following package:</p>";
            // Display the chosen package details
            $sql_selected_package = "SELECT * FROM holiday_package WHERE package_id='$selected_package_id'";
            $result_selected_package = $conn->query($sql_selected_package);
            if ($result_selected_package->num_rows > 0) {
                $row_selected_package = $result_selected_package->fetch_assoc();
                echo "<p>Package ID: " . $row_selected_package['package_id'] . "</p>";
                echo "<p>Location: " . $row_selected_package['location'] . "</p>";
                echo "<p>Destination: " . $row_selected_package['destination'] . "</p>";
                echo "<p>Duration (night): " . $row_selected_package['duration'] . "</p>";
                echo "<p>Amenities: " . $row_selected_package['amenities'] . "</p>";
            }
        } else {
            echo "<p class='message'>Error choosing holiday package: " . $conn->error . "</p>";
        }
    } ?>
    <button onclick="window.location.href = 'employee_dashboard.php';" class="back-button">Back</button>
</div>

</body>
</html>
