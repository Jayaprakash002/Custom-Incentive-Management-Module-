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

// Initialize variables
$package_id = "";
$location = "";
$destination = "";
$duration = "";
$amenities = "";
$update = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_package'])) {
        $package_id = $_POST['package_id'];
        $location = $_POST['location'];
        $destination = $_POST['destination'];
        $duration = $_POST['duration'];
        $amenities = $_POST['amenities'];

        $sql_insert_package = "INSERT INTO holiday_package (package_id, location, destination, duration, amenities) 
                                VALUES ('$package_id', '$location', '$destination', '$duration', '$amenities')";

        if ($conn->query($sql_insert_package) === TRUE) {
            // Package added successfully
            $package_id = "";
            $location = "";
            $destination = "";
            $duration = "";
            $amenities = "";
        } else {
            echo "Error adding package: " . $conn->error;
        }
    } elseif (isset($_POST['edit_package'])) {
        // Edit package
        $package_id = $_POST['package_id'];
        $location = $_POST['location'];
        $destination = $_POST['destination'];
        $duration = $_POST['duration'];
        $amenities = $_POST['amenities'];

        $sql_update_package = "UPDATE holiday_package SET location='$location', destination='$destination', duration='$duration', amenities='$amenities' 
                                WHERE package_id='$package_id'";

        if ($conn->query($sql_update_package) === TRUE) {
            echo "Package updated successfully";
        } else {
            echo "Error updating package: " . $conn->error;
        }
    /*} elseif (isset($_POST['delete_package'])) {
        // Delete package
        $package_id = $_POST['package_id'];

        $sql_delete_package = "DELETE FROM holiday_package WHERE package_id='$package_id'";

        if ($conn->query($sql_delete_package) === TRUE) {
            echo "Package deleted successfully";
        } else {
            echo "Error deleting package: " . $conn->error;
        }
    }
 }*/
} elseif (isset($_POST['delete_package'])) {
    // Delete package
    $package_id = $_POST['package_id'];

    // Check if there are any related entries in claim_package table
    $sql_check_claim = "SELECT * FROM claim_package WHERE package_id='$package_id'";
    $result_check_claim = $conn->query($sql_check_claim);

    if ($result_check_claim->num_rows > 0) {
        // If there are related entries, delete them first
        $sql_delete_claim = "DELETE FROM claim_package WHERE package_id='$package_id'";
        if ($conn->query($sql_delete_claim) === TRUE) {
            // Then delete the package
            $sql_delete_package = "DELETE FROM holiday_package WHERE package_id='$package_id'";
            if ($conn->query($sql_delete_package) === TRUE) {
                echo "Package and related entries deleted successfully";
            } else {
                echo "Error deleting package: " . $conn->error;
            }
        } else {
            echo "Error deleting related entries: " . $conn->error;
        }
    } else {
        // If there are no related entries, directly delete the package
        $sql_delete_package = "DELETE FROM holiday_package WHERE package_id='$package_id'";
        if ($conn->query($sql_delete_package) === TRUE) {
            echo "Package deleted successfully";
        } else {
            echo "Error deleting package: " . $conn->error;
        }
    }
}
}

// Fetch all packages
$sql_select_packages = "SELECT * FROM holiday_package";
$result_packages = $conn->query($sql_select_packages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Holiday Package</title>
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

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"], input[type="button"] {
            width: auto;
            background-color: #4caf50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover, input[type="button"]:hover {
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
    </style>
</head>
<body>

<div class="container">
    <h2>Add Holiday Package</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="package_id">Package ID:</label>
        <input type="text" id="package_id" name="package_id" value="<?php echo $package_id; ?>" required>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>

        <label for="destination">Destination:</label>
        <input type="text" id="destination" name="destination" value="<?php echo $destination; ?>" required>

        <label for="duration">Duration (night):</label>
        <input type="number" id="duration" name="duration" value="<?php echo $duration; ?>" required>

        <label for="amenities">Amenities:</label>
        <textarea id="amenities" name="amenities" rows="4" required><?php echo $amenities; ?></textarea>

        <?php if ($update) { ?>
            <input type="submit" name="edit_package" value="Update Package">
        <?php } else { ?>
            <input type="submit" name="add_package" value="Add Package">
        <?php } ?>
    </form>

    <h2>Holiday Packages</h2>
    <?php if ($result_packages->num_rows > 0) { ?>
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
                            <input type="hidden" name="package_id" value="<?php echo $row_package['package_id']; ?>">
                            <input type="submit" name="delete_package" value="Delete">
                            <input type="button" value="Edit" onclick="editPackage('<?php echo $row_package['package_id']; ?>',
                                '<?php echo $row_package['location']; ?>', '<?php echo $row_package['destination']; ?>',
                                '<?php echo $row_package['duration']; ?>', '<?php echo $row_package['amenities']; ?>')">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else {
        echo "No holiday packages found.";
    } ?>
    <button onclick="window.location.href = 'admin_dashboard.php';" class="back-button">Back</button>
</div>

<script>
    function editPackage(package_id, location, destination, duration, amenities) {
        document.querySelector('input[name="package_id"]').value = package_id;
        document.querySelector('input[name="location"]').value = location;
        document.querySelector('input[name="destination"]').value = destination;
        document.querySelector('input[name="duration"]').value = duration;
        document.querySelector('textarea[name="amenities"]').value = amenities;
        document.querySelector('input[name="add_package"]').value = "Update Package";
    }
</script>

</body>
</html>
