<?php
// Configuration for Database Connection
$host = 'localhost';
$dbname = 'porsche_clone';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

try {
    // Create a new PDO instance (PHP Data Object)
    $dsn = "mysql:host=$host;port=3307;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Set PDO to throw exceptions on errors so we can catch them easily
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // If the connection fails, stop the script and print the error
    die("Database Connection failed: " . $e->getMessage());
}
?>
