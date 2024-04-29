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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['name'];
    $daily_sale = $_POST['daily_sale'];

    //insert data into sales table
    $sql_insert_sale = "INSERT INTO sales (user_id, name, daily_sale) 
                        VALUES ('$user_id', '$name', $daily_sale)";

    if ($conn->query($sql_insert_sale) === TRUE) {
        $message = "Daily sales added successfully";
    } else {
        $message = "Error adding daily sales: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Daily Sales</title>
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
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #4caf50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            color: #333;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Daily Sales</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="daily_sale">Daily Sales:</label><br>
        <input type="text" id="daily_sale" name="daily_sale" required><br>

        <input type="submit" value="Add Sales">
    </form>
    <?php if(isset($message)) { ?>
    <p class="message"><?php echo $message; ?></p>
    <?php } ?>
    <button onclick="window.location.href = 'employee_dashboard.php';" class="back-button">Back</button>
</div>

</body>
</html>
