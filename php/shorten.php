<?php
require '../config.php'; // Include the configuration file for database connection
include '../php/time.php'; // Include the time-related functions
include '../phpqrcode/qrlib.php'; // Include the QR code library

// Function to generate a random short code of specified length
function generateShortCode($length)
{
    // Shuffle the characters and return a substring of the specified length
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

$result_status = null; // Initialize the result status variable
$shortURL = null; // Initialize the short URL variable

if ($_POST) { // Check if the form has been submitted
    $originalUrl = $_POST['url']; // Get the original URL from the form
    $customCode = $_POST['custom_code'] ?? null; // Get the custom code if provided
    $codeLength = !empty($_POST['code_length']) ? intval($_POST['code_length']) : 6; // Determine the code length
    $expiresAt = $_POST['expires_at'] ?? null; // Get the expiration date if provided
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash the password if provided
    $usageLimit = $_POST['usage_limit'] ?? null; // Get the usage limit if provided
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get the user ID from the session if logged in

    // Choose the short code based on user input or generate a new one
    if ($customCode) {
        $shortCode = $customCode; // Use the custom code if provided
    } else {
        $shortCode = generateShortCode($codeLength); // Generate a new short code
    }

    // Check if the short code already exists in the database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM urls WHERE short_code = ?"); // Prepare the SQL statement
    $stmt->execute([$shortCode]); // Execute the statement with the short code
    $count = $stmt->fetchColumn(); // Fetch the count of existing short codes

    if ($count > 0) { // If the short code already exists
        $result_status = "не беше създаден правилно: краткият URL вече съществува."; // Set the result status to indicate failure
    } else {
        // Insert the new URL data into the database
        $stmt = $pdo->prepare("INSERT INTO urls (user_id, short_code, original_url, expires_at, password_hash, usage_limit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $shortCode, $originalUrl, $expiresAt, $password, $usageLimit]); // Execute the insert statement

        // Generate the QR code for the short URL
        $qrPath = '../qrcodes/' . $shortCode . '.png'; // Define the path for the QR code image
        QRcode::png("localhost/$shortCode", $qrPath, QR_ECLEVEL_L, 5, 0); // Generate the QR code

        $shortURL = "http://localhost/$shortCode"; // Set the short URL
        $result_status = "успешно създаден. 🎉"; // Set the result status to indicate success
    }
}
?>

