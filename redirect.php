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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>Login</title> <!-- Page title -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Link to custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Link to Bootstrap CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- Link to AOS CSS -->
</head>

<body class="index"> <!-- Body class for styling -->
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-75 my-5 border-custom box-shadow-custom mx-auto justify-content-end row">
        <!-- Main container with animations -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"><a href="index.php"
                data-bs-toggle="tooltip" data-bs-placement="right" role="button" data-bs-title="Home"
                data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover "
                style="color: #fff;"><ion-icon name="home-outline" class="me-1"></ion-icon></a></div>
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <p style="color: white;" class="text-center m-0 welcomeGreeting"><?php if (isset($_SESSION['user_id'])) {
                echo $result_welcomeGreeting . ", " . htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']);
            } else {
                echo $result_welcomeGreeting . "!";
            } ?></p> <!-- Display welcome message -->
        </div>
        <div class="col-12 col-lg d-flex justify-content-around">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/php/register.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Register" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center justify-content-center"
                    style="color: #fff;"><ion-icon name="person-circle-outline" class="mt-4 mb-3"></ion-icon></a>

            <?php endif ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/php/dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Your Links" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center" style="color: #fff;"><ion-icon
                        name="clipboard-outline" class="me-1"></ion-icon><span class="menu-item"></span></a>
                <a href="/php/logout.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Logout" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover ms-4 d-flex align-items-center" style="color: #fff;"><ion-icon
                        name="exit-outline" class="me-1"></ion-icon><span class="menu-item"></span></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="mb-5 container align-items-center  d-flex justify-content-center">
        <div data-aos="fade-up" data-aos-duration="1500" class="w-40 p-4 bg-white box-shadow-custom border-custom">

            <h2 class="text-center"><?php echo $result_title; ?></h2> <!-- Display result title -->
            <?php if (isset($result_passwordincorrect)) {
                echo $result_passwordincorrect; // Display incorrect password message
            } ?>
            <?php if (isset($result_password)) {
                echo $result_password; // Display password input form
            } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script> <!-- Link to Bootstrap JS -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Link to Ionicons -->
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Fallback for older browsers -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]') // Select all elements with tooltip
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)) // Initialize Bootstrap tooltips
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <!-- Link to AOS JS -->
    <script>
        AOS.init(); // Initialize AOS animations
    </script>
</body>

</html>