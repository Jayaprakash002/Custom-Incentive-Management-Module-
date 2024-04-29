<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "incentive_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$role = $_POST['role'];
$user_id = $_POST['user_id'];
$password = $_POST['password'];

//checking the user data
$sql = "SELECT * FROM user WHERE role='$role' AND user_id='$user_id' AND password='$password'";
$result = $conn->query($sql);

if ($result === false) {
    
    echo "Error executing query: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        
        $user_data = $result->fetch_assoc();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $user_data['name']; // Store the user's name,role,user id in the session
        if ($role == 'admin') {
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
        } else {
            header("Location: employee_dashboard.php"); // Redirect to employee dashboard
        }
    } else {
        echo "Invalid user ID, password, or role";
    }
}

$conn->close();
?>
