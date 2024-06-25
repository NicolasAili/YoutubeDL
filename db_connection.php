<?php
// Database connection details
$host = 'localhost';
$dbname = 'youtubedl';
$username = 'phpmyadmin';
$password = 'admin';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    error_log("Connection failed: " . $e->getMessage());
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
