<?php
session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
        }

        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: 180px;
        }

        .button-green {
            background-color: #4caf50;
            color: #fff;
        }

        .button-blue {
            background-color: #007bff;
            color: #fff;
        }

        .button-red {
            background-color: #dc3545;
            color: #fff;
        }

        .button-orange {
            background-color: #ffc107;
            color: #212529;
        }
        .button-yellow {
            background-color: #32a852;
            color: #1227a1;
        }

        .button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, Admin!</h1>
        <div class="button-container">
            <a href="add_employee.php" class="button button-green">Add Employee</a>
            <a href="add_holiday_package.php" class="button button-blue">Add Holiday Package</a>
            <a href="view_daily_sales.php" class="button button-red">View Daily Sales</a>
            <a href="view_package_claims.php" class="button button-orange">View Package Claims</a>
            <a href="send_notification.php" class="button button-yellow">Send Notification</a>

        </div>
        <button onclick="window.location.href = 'index.html';" class="back-button">logout</button>
    </div>
</body>
</html>
