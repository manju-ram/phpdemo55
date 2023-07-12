<?php
// Connect to the MySQL database
$servername = "139.59.88.6";
$username = "admin1";
$password = "Aapl@123";
$dbname = "elagori";

// Get the JSON data from the POST request
$jsonData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($jsonData, true);

// Check if the data is not empty
if (!empty($data)) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if data already exists in the database
    $result = $conn->query("SELECT COUNT(*) as count FROM data_table");
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
        // Data already exists, perform an update
        $stmt = $conn->prepare("UPDATE data_table SET Value = ? WHERE Channel = ?");

        foreach ($data as $item) {
            $channel = $item['CHANNEL'];
            $value = $item['VALUE'];

            $stmt->bind_param("di", $value, $channel);
            $stmt->execute();
        }

        $stmt->close();
        echo "Data updated successfully!";
    } else {
        // Data does not exist, perform an insert
        $stmt = $conn->prepare("INSERT INTO data_table (Channel, Value) VALUES (?, ?)");

        foreach ($data as $item) {
            $channel = $item['CHANNEL'];
            $value = $item['VALUE'];

            $stmt->bind_param("di", $channel, $value);
            $stmt->execute();
        }

        $stmt->close();
        echo "Data inserted successfully!";
    }

    // Close the database connection
    $conn->close();
} else {
    // Connect to the MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if data exists in the table
    $result = $conn->query("SELECT COUNT(*) as count FROM data_table");
    $row = $result->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
        // Delete all data from the table
        $conn->query("TRUNCATE TABLE data_table");

        echo "Data cleared successfully!";
    } else {
        echo "No data received!";
    }

    // Close the database connection
    $conn->close();
}
?>
