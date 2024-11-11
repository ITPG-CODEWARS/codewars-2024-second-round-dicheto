<?php
require '../config.php'; // Include the configuration file for database connection
include '../php/time.php'; // Include the time-related functions

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /php/login.php'); // Redirect to login page if user is not logged in
    exit(); // Stop script execution after redirection
}

$userId = $_SESSION['user_id']; // Store the logged-in user's ID

// Handle delete request via GET parameter
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id']; // Get the ID of the link to delete

    // Delete the link if it belongs to the current user
    $stmt = $pdo->prepare("DELETE FROM urls WHERE id = ? AND user_id = ?"); // Prepare SQL statement for deletion
    $stmt->execute([$deleteId, $userId]); // Execute the statement with the link ID and user ID

    // Message for successful deletion
    $deleteMessage = $stmt->rowCount() ? "Линкът е успешно изтрит." : "Грешка при изтриването на линка"; // Set message based on deletion result
}

// Fetch the user's links
$stmt = $pdo->prepare("SELECT id, short_code, original_url, expires_at, usage_limit, click_count, password_hash FROM urls WHERE user_id = ?"); // Prepare SQL statement to fetch user's links
$stmt->execute([$userId]); // Execute the statement with the user ID
$links = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all links as an associative array
?>
