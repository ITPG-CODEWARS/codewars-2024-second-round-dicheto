<?php
require 'config.php'; // Include the configuration file for database connection
include './php/time.php'; // Include the time-related functions

$shortCode = $_GET['code'] ?? null; // Retrieve the short code from the URL parameters, default to null
$result_title = ""; // Variable for messages

if ($shortCode) { // Check if a short code was provided
    $stmt = $pdo->prepare("SELECT * FROM urls WHERE short_code = ?"); // Prepare SQL statement to fetch URL data
    $stmt->execute([$shortCode]); // Execute the statement with the provided short code
    $urlData = $stmt->fetch(); // Fetch the URL data

    if ($urlData) { // Check if URL data was found
        // Check if the link has expired
        if (strtotime($urlData['expires_at']) < time() && $urlData['expires_at'] != "0000-00-00 00:00:00") {
            $result_title = "Линкът е изтекъл."; // Set message for expired link
        }
        // Check for password
        elseif ($urlData['password_hash']) {
            // If there is a POST request, verify the entered password
            if ($_POST && password_verify($_POST['password'], $urlData['password_hash'])) {
                // Check usage limit after successful password verification
                if (isset($urlData['usage_limit']) && $urlData['click_count'] >= $urlData['usage_limit'] && $urlData !== 0) {
                    $result_title = "Лимитът на използвания е превишен."; // Set message for exceeded usage limit
                } else {
                    // Increment click_count and redirect to the original URL
                    $stmt = $pdo->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = ?"); // Prepare SQL to update click count
                    $stmt->execute([$shortCode]); // Execute the update statement
                    header("Location: " . $urlData['original_url']); // Redirect to the original URL
                    exit; // Exit to prevent further HTML execution after redirection
                }
            } else {
                // Show password input form
                $result_title = "Удостоверяване"; // Set message for authentication
                $result_password = '<form method="post" class="my-4">
                    <input type="password" name="password" placeholder="Парола" class="mx-auto mb-3" required>
                    <button type="submit" class="btn-submit px-3 d-flex justify-content-center">Влизане</button>
                </form>'; // Create password input form
                if ($_POST) {
                    $result_passwordincorrect = '<div class="alert alert-danger" role="alert">Грешна парола</div>'; // Set message for incorrect password
                }
            }
        } else {
            // Check usage limit when there is no password
            if (isset($urlData['usage_limit']) && $urlData['click_count'] >= $urlData['usage_limit'] && $urlData['usage_limit'] !== 0) {
                $result_title = "Лимитът на използванията е превишен."; // Set message for exceeded usage limit
            } else {
                // Increment click_count and directly redirect
                $stmt = $pdo->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = ?"); // Prepare SQL to update click count
                $stmt->execute([$shortCode]); // Execute the update statement
                header("Location: " . $urlData['original_url']); // Redirect to the original URL
                exit; // Exit to prevent further HTML execution after redirection
            }
        }
    } else {
        $result_title = "Линкът не е намерен"; // Set message for link not found
    }
} else {
    $result_title = "Не е предоставен линк"; // Set message for no code provided
}
?>
