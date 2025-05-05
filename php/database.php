<!-- <?php
// Database connection configuration
$host = '127.0.0.1'; // Use IP instead of 'localhost'
$username = 'root';  // Your database username (default is often root for XAMPP/WAMP)
$password = '';      // Your database password (often empty for local development)
$database = 'hotel_db'; // Your database name
$port = 3306;        // Default MySQL port

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection with error handling
try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database, $port);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper handling of special characters
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?> -->

<?php
// Database connection configuration using PDO
$host = '127.0.0.1'; // Use IP instead of 'localhost'
$username = 'root';  // Your database username
$password = '';      // Your database password
$database = 'hotel_db'; // Your database name
$port = 3306;        // Default MySQL port

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $conn = new PDO($dsn, $username, $password, $options);
    // Connection successful
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>