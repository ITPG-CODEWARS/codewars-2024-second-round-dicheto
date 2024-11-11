<?php
// Database configuration
$host = 'HOST'; // Host address for the database connection
$db = 'DATABASE'; // Name of the database
$user = 'USERNAME'; // Username for database access
$pass = 'PASSWORD'; // Password for the database user

try {
    // Establishing a new PDO connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Setting the error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Creating the users table if it does not already exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        first_name VARCHAR(50) NULL,
        last_name VARCHAR(50) NULL
    )");

// Creating the urls table if it does not already exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS urls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        short_code VARCHAR(255) NOT NULL UNIQUE,
        original_url TEXT NOT NULL,
        expires_at DATETIME NULL,
        password_hash VARCHAR(255) NULL,
        usage_limit INT DEFAULT NULL,
        click_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

} catch (PDOException $e) {
    // Handling connection errors by terminating the script and displaying the error message
    die("Connection failed: " . $e->getMessage());
}

// Starting the user session
session_start(); // Initializes session data for the user
