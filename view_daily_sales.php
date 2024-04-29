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

$selected_user_id = "";
$selected_date = date("Y-m-d");
$total_sales = 0;
$incentive_percentage = 0;
$eligible_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_user_id = $_POST['user_id'];
    $selected_date = $_POST['selected_date'];
}

// Get total sales till the selected date for the selected employee
$sql_total_sales = "SELECT SUM(daily_sale) AS total_sales FROM sales WHERE user_id='$selected_user_id' AND DATE(date_time) <= '$selected_date'";
$result_total_sales = $conn->query($sql_total_sales);
if ($result_total_sales->num_rows > 0) {
    $row_total_sales = $result_total_sales->fetch_assoc();
    $total_sales = $row_total_sales['total_sales'];

    // Calculate incentive based on total sales
    if ($total_sales >= 10000 && $total_sales < 20000) {
        $incentive_percentage = 1.5;
        $eligible_message = "Congratulations! Eligible for a 1.5% incentive.";
    } elseif ($total_sales >= 20000 && $total_sales < 30000) {
        $incentive_percentage = 3;
        $eligible_message = "Congratulations! Eligible for a 3% incentive.";
    } elseif ($total_sales >= 30000 && $total_sales < 50000) {
        $incentive_percentage = 3.5;
        $eligible_message = "Congratulations! Eligible for a 3.5% incentive and a $1000 bonus.";
    } elseif ($total_sales >= 50000) {
        $incentive_percentage = 5;
        $eligible_message = "Congratulations! Eligible for a 5% incentive and a holiday package.";
    } else {
        $eligible_message = "Keep up the good work!";
    }
}

// Fetch user IDs for dropdown
$sql_user_ids = "SELECT user_id FROM user WHERE role='employee'";
$result_user_ids = $conn->query($sql_user_ids);

// Fetch sales data based on selected user ID and date
$sql_sales_data = "SELECT * FROM sales WHERE user_id='$selected_user_id' AND DATE(date_time)='$selected_date'";
$result_sales_data = $conn->query($sql_sales_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Daily Sales</title>
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

select, input[type="date"], input[type="submit"] {
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
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

.total-sales {
    margin-top: 20px;
}

.incentive-details {
    margin-top: 20px;
}

.back-button {
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

.back-button:hover {
    background-color: #45a049;
}

    </style>
</head>
<body>

<div class="container">
    <h2>View Daily Sales</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="user_id">Select Employee:</label>
        <select id="user_id" name="user_id">
            <option value="">All Employees</option>
            <?php
            if ($result_user_ids->num_rows > 0) {
                while ($row_user_ids = $result_user_ids->fetch_assoc()) {
                    $selected = ($row_user_ids['user_id'] == $selected_user_id) ? "selected" : "";
                    echo "<option value='" . $row_user_ids['user_id'] . "' $selected>" . $row_user_ids['user_id'] . "</option>";
                }
            }
            ?>
        </select>

        <label for="selected_date">Select Date:</label>
        <input type="date" id="selected_date" name="selected_date" value="<?php echo $selected_date; ?>">

        <input type="submit" value="View Sales">
    </form>

    <?php if ($result_sales_data->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Daily Sale</th>
            </tr>
            <?php
            while ($row_sales_data = $result_sales_data->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row_sales_data['date_time'] . "</td>";
                echo "<td>" . $row_sales_data['daily_sale'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php } else {
        echo "No sales data found.";
    } ?>

    <div class="total-sales">
        <label>Total Sales Till Date:</label>
        <span><?php echo $total_sales; ?></span>
    </div>

    <div class="incentive-details">
        <p><?php echo $eligible_message; ?></p>
        <p><?php echo "Incentive Percentage: " . $incentive_percentage . "%"; ?></p>
    </div>

    <button onclick="window.location.href = 'admin_dashboard.php';" class="back-button">Back</button>
</div>

</body>
</html>
