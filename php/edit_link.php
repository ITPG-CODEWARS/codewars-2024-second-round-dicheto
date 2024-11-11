<?php


require '../config.php'; // Including the configuration file for database connection
include '../php/time.php'; // Including a file for time-related functions

if (!isset($_SESSION['user_id'])) {
    header("Location: /php/login.php"); // Redirect to login page if user is not authenticated
    exit(); // Terminate the script after redirection
}

$linkId = $_GET['id'] ?? null; // Get the link ID from the URL, default to null if not set
$userId = $_SESSION['user_id']; // Retrieve the current user's ID from the session

// Fetching the data for the specific link from the database
$stmt = $pdo->prepare("SELECT * FROM urls WHERE id = ? AND user_id = ?"); // Prepare SQL statement to select link
$stmt->execute([$linkId, $userId]); // Execute the statement with link ID and user ID
$link = $stmt->fetch(); // Fetch the link data

if (!$link) {
    $result_edit = "<div class='alert alert-danger' role='alert'>Линкът не е намерен или нямате достъп до него!</div>"; // Error message if link is not found or access is denied
    $disabled = "d-none"; // Disable the form if the link is not found
}

if ($_POST) { // Check if the form has been submitted
    $newUrl = $_POST['original_url']; // Get the new URL from the form submission
    $expiresAt = $_POST['expires_at'] ?? null; // Get the expiration date if provided
    $usageLimit = $_POST['usage_limit'] ?? null; // Get the usage limit if provided
    $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $link['password_hash']; // Hash the new password if provided, otherwise use the existing one

    // Updating the link data
    $stmt = $pdo->prepare("UPDATE urls SET original_url = ?, expires_at = ?, usage_limit = ?, password_hash = ? WHERE id = ? AND user_id = ?"); // Prepare SQL statement to update link
    $stmt->execute([$newUrl, $expiresAt, $usageLimit, $newPassword, $linkId, $userId]); // Execute the update statement

    $result_edit = "<div class='alert alert-success' role='alert'>Промяната е успешно записана!</div>"; // Success message after updating the link
}
?>


<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8"> <!-- Set character encoding to UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design for mobile devices -->
    <title>Редактиране на линк</title> <!-- Page title -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Link to custom CSS stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Link to Bootstrap CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- Link to AOS (Animate On Scroll) CSS -->

</head>
