<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS incentive_db";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the created database
$conn->select_db("incentive_db");

// Create user table
$sql_create_user_table = "CREATE TABLE IF NOT EXISTS user (
    user_id VARCHAR(100)  PRIMARY KEY,
    role ENUM('admin', 'employee'),
    name VARCHAR(100),
    email VARCHAR(100),
    phone_no VARCHAR(20),
    password VARCHAR(100)
)";
if ($conn->query($sql_create_user_table) === TRUE) {
    echo "Table 'user' created successfully<br>";
} else {
    echo "Error creating table 'user': " . $conn->error . "<br>";
}

// Create sales table
$sql_create_sales_table = "CREATE TABLE IF NOT EXISTS sales (
    user_id VARCHAR(100),
    name VARCHAR(100),
    daily_sale INT,
    date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
if ($conn->query($sql_create_sales_table) === TRUE) {
    echo "Table 'sales' created successfully<br>";
} else {
    echo "Error creating table 'sales': " . $conn->error . "<br>";
}

// Create holiday_package table
$sql_create_holiday_package_table = "CREATE TABLE IF NOT EXISTS holiday_package (
    package_id VARCHAR(100)  PRIMARY KEY,
    location VARCHAR(100),
    destination VARCHAR(100),
    duration INT,
    amenities TEXT
)";
if ($conn->query($sql_create_holiday_package_table) === TRUE) {
    echo "Table 'holiday_package' created successfully<br>";
} else {
    echo "Error creating table 'holiday_package': " . $conn->error . "<br>";
}

// Create claim_package table
$sql_create_claim_package_table = "CREATE TABLE IF NOT EXISTS claim_package (
    package_id VARCHAR(100),
    user_id VARCHAR(100),
    name VARCHAR(100),
    date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (package_id) REFERENCES holiday_package(package_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
if ($conn->query($sql_create_claim_package_table) === TRUE) {
    echo "Table 'claim_package' created successfully<br>";
} else {
    echo "Error creating table 'claim_package': " . $conn->error . "<br>";
}
// Insert admin data into user table for admin access
$sql_insert_user = "INSERT INTO user (user_id, role, name, email, phone_no, password) 
                    VALUES ('admin123', 'admin', 'admin', 'abc@email.com', '2452858585', 'admin123')";
if ($conn->query($sql_insert_user) === TRUE) {
    echo "Data inserted into 'user' table successfully<br>";
} else {
    echo "Error inserting data into 'user' table: " . $conn->error . "<br>";
}
$conn->close();
?>
