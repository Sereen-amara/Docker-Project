<?php
$servername = "db";  // Use the Docker Compose service name for MySQL
$username = "root";
$password = "root";
$dbname = "mydatabase";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example vulnerable query
$id = $_GET['id'];  // Vulnerable to SQL Injection
$sql = "SELECT * FROM users WHERE id = $id";
$result = $conn->query($sql);

// Display results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
