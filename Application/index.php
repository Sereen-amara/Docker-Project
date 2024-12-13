<?php
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;


// Autoload dependencies (ensure Prometheus client is installed via Composer)
require 'vendor/autoload.php';

// Set up Prometheus in-memory adapter and registry
$adapter = new InMemory();
$registry = new CollectorRegistry($adapter);

// Create metrics
$counter = $registry->getOrRegisterCounter('php_app', 'request_count', 'Counts requests', ['method']);
$gauge = $registry->getOrRegisterGauge('php_app', 'memory_usage', 'Memory usage');

// Increment request counter for every GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $counter->incBy(1, ['GET']);
}

// Record memory usage as a gauge metric
$gauge->set(memory_get_usage());

// Expose metrics on /metrics endpoint
if ($_SERVER['REQUEST_URI'] === '/metrics') {
    header('Content-Type: text/plain; charset=utf-8');
    $renderer = new RenderTextFormat();
    echo $renderer->render($registry->getMetricFamilySamples());
    exit;
}

// Main PHP App Logic (Example)
echo "Hello, world!";



$servername = "db";  
$username = "root";
$password = "root";
$dbname = "mydatabase";

// Hardcoded secrets (Trivy can detect these with the --scanners secret flag)
$api_key = "hardcoded_api_key"; // Insecure API key
$db_password = "hardcoded_password"; // Hardcoded sensitive password
$jwt_secret = "my_jwt_secret_key"; // Example of a hardcoded JWT secret


// Connecting to MySQL (1: Using plain-text credentials)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // (2: Exposing detailed error information to the user)
    die("Connection failed: " . $conn->connect_error);
}

// (3: Missing HTTPS enforcement, no secure cookies)

// Vulnerable query (4: SQL Injection via unsanitized user input)
$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id"; // No sanitization or prepared statement
$result = $conn->query($sql);

// Displaying results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // (5: Cross-Site Scripting - Reflecting user data directly without sanitization)
        echo "id: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
    }
} else {
    // (6: Exposing internal logic information)
    echo "0 results";
}

// (7: Missing CSRF protection on actions)
// Example of an insecure form submission
echo '
<form method="post" action="delete.php">
    <input type="hidden" name="id" value="' . $_GET['id'] . '">
    <button type="submit">Delete User</button>
</form>
';

// (8: Lack of rate limiting or CAPTCHA to prevent brute-force attacks)
// Example of insecure login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // (9: Using MD5 for password hashing - insecure hashing algorithm)
    $hashed_password = md5($password);
    $login_sql = "SELECT * FROM users WHERE username = '$username' AND password = '$hashed_password'";
    $login_result = $conn->query($login_sql);

    if ($login_result->num_rows > 0) {
        // (10: Setting insecure cookies, no HTTPOnly or Secure flag)
        setcookie("auth_token", "user_session_token", time() + 3600);
        echo "Login successful!";
    } else {
        // (11: Exposing invalid credentials error message)
        echo "Invalid username or password.";
    }
}

// (12: File upload without validation)
if (isset($_FILES['userfile'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["userfile"]["name"]);
    move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file); // No file type/size checks
    echo "File uploaded to " . $target_file;
}

// (13: Directory Traversal vulnerability in file access)
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    readfile($file); // No path sanitization
}

// (14: Unsecured error reporting)
error_reporting(E_ALL); // Exposing sensitive application details in error messages

// (15: No database input/output sanitization)
if (isset($_POST['comment'])) {
    $comment = $_POST['comment']; // No sanitization
    $sql = "INSERT INTO comments (comment) VALUES ('$comment')";
    $conn->query($sql);
    echo "Comment added!";
}

$conn->close();
?>
