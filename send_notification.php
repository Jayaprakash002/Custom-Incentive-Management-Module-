<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "incentive_db"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee details including email, performance metrics, incentive details, and holiday package eligibility
$sql_select_employees = "SELECT u.email, u.name, SUM(s.daily_sale) AS total_sales, 
                                CASE
                                    WHEN SUM(s.daily_sale) >= 50000 THEN '5%'
                                    WHEN SUM(s.daily_sale) >= 30000 THEN '3.5%'
                                    WHEN SUM(s.daily_sale) >= 20000 THEN '3%'
                                    WHEN SUM(s.daily_sale) >= 10000 THEN '1.5%'
                                    ELSE 'No incentive'
                                END AS incentive_percentage,
                                CASE
                                    WHEN SUM(s.daily_sale) >= 50000 THEN 'Eligible'
                                    ELSE 'Not eligible'
                                END AS holiday_package_eligibility
                          FROM user u
                          LEFT JOIN sales s ON u.user_id = s.user_id
                          GROUP BY u.user_id";

$result_employees = $conn->query($sql_select_employees);

// Loop through each employee to send email notifications
while ($row_employee = $result_employees->fetch_assoc()) {
    $to = $row_employee['email'];
    $subject = "Daily Performance and Incentive Details";

    $message = "Dear " . $row_employee['name'] . ",\r\n\r\n";
    $message .= "Here are your performance metrics for the day:\r\n";
    $message .= "Total Sales: $" . $row_employee['total_sales'] . "\r\n";
    $message .= "Incentive Percentage: " . $row_employee['incentive_percentage'] . "\r\n";
    $message .= "Holiday Package Eligibility: " . $row_employee['holiday_package_eligibility'] . "\r\n\r\n";
    $message .= "Thank you for your hard work!\r\n";

    $headers = "From: your_email@example.com\r\n";
    $headers .= "Reply-To: your_email@example.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully to " . $to . "<br>";
    } else {
        echo "Failed to send email to " . $to . "<br>";
    }
}
$conn->close();
?>
