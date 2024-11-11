<?php
require '../config.php'; // Include the configuration file for database connection
include '../php/time.php'; // Include the time-related functions

if ($_POST) { // Check if the form has been submitted
    $firstName = $_POST['first_name']; // Retrieve the first name from the form
    $lastName = $_POST['last_name']; // Retrieve the last name from the form
    $username = $_POST['username']; // Retrieve the username (email) from the form
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Check for existing username
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?"); // Prepare SQL statement to count users with the same username
    $stmt->execute([$username]); // Execute the statement with the provided username
    $emailExists = $stmt->fetchColumn(); // Fetch the count of existing usernames

    if ($emailExists > 0) { // If the username already exists
        $error_register = "<div class='alert alert-danger' role='alert'>С този имейл има вече направена регистрация. Използвайте друг или влезте!</div>"; // Set error message for existing username
    } else {
        // If the username does not exist, proceed with registration
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password_hash) VALUES (?, ?, ?, ?)"); // Prepare SQL statement to insert new user
        $stmt->execute([$firstName, $lastName, $username, $password]); // Execute the statement with user data

        $_SESSION['user_id'] = $pdo->lastInsertId(); // Store the new user's ID in the session
        header("Location: ../index.php"); // Redirect to the index page after successful registration
        exit(); // Terminate the script
    }
}
?>
