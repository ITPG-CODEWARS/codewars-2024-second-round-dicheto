<?php

// Include the configuration file for database connection
require '../config.php'; // Load database configuration settings
// Include the time-related functions
include '../php/time.php'; // Load custom time functions

// Check if the form has been submitted
if ($_POST) { // Proceed if the form is submitted
    // Retrieve the username and password from the POST request
    $username = $_POST['username']; // Get the username from the form
    $password = $_POST['password']; // Get the password from the form
    $result = null; // Initialize result variable for feedback

    // Prepare a SQL statement to select the user by username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); // Prepare SQL query
    // Execute the prepared statement with the provided username
    $stmt->execute([$username]); // Execute the query with the username
    // Fetch the user data from the database
    $user = $stmt->fetch(); // Retrieve user data

    // Verify if the user exists and the password is correct
    if ($user && password_verify($password, $user['password_hash'])) { // Check user existence and password validity
        // Store user information in session variables upon successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['first_name'] = $user['first_name']; // Store user's first name in session
        $_SESSION['last_name'] = $user['last_name']; // Store user's last name in session
        // Redirect to the index page after successful login
        header("Location: ./../index.php"); // Redirect to homepage
        exit(); // Terminate the script after redirection
    } else {
        // Set an error message for invalid login credentials
        $result = "<div class='alert alert-danger' role='alert'>Invalid username or password.</div>"; // Prepare error message
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>Лог-ин</title> <!-- Page title -->
    <!-- Link to the main stylesheet -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Main CSS file -->
    <!-- Link to Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- AOS CSS for animations -->
    <style>
        /* Responsive design for smaller screens */
        @media (max-width:500px) {
            .w-50 {
                width: 75% !important; // Adjust width for small screens
            }
        }
    </style>
</head>

<body class="index">
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-50 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <a href="../../index.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                data-bs-title="Начало" data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover"
                style="color: #fff;">
                <ion-icon name="home-outline" class="me-1"></ion-icon> <!-- Home icon -->
            </a>
        </div>
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <p style="color: white;" class="text-center m-0 welcomeGreeting">
                <?php if (isset($_SESSION['user_id'])) { // Check if user is logged in
                        echo $result_welcomeGreeting . ", " . htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']); // Display welcome message with user's name
                    } else {
                        echo $result_welcomeGreeting . "!"; // Display welcome message for guests
                    } ?>
            </p>
        </div>
        <div class="col-12 col-lg d-flex justify-content-around">
            <?php if (!isset($_SESSION['user_id'])): // If user is not logged in ?>
                <a href="/php/register.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Регистрация" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center justify-content-center"
                    style="color: #fff;">
                    <ion-icon name="person-circle-outline" class="mt-4 mb-3"></ion-icon> <!-- Registration icon -->
                </a>
            <?php endif ?>
            <?php if (isset($_SESSION['user_id'])): // If user is logged in ?>
                <a href="/php/dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Вашите линкове" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center" style="color: #fff;">
                    <ion-icon name="clipboard-outline" class="me-1"></ion-icon><span class="menu-item"></span>
                    <!-- Dashboard icon -->
                </a>
                <a href="/php/logout.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Изход" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover ms-4 d-flex align-items-center" style="color: #fff;">
                    <ion-icon name="exit-outline" class="me-1"></ion-icon><span class="menu-item"></span>
                    <!-- Logout icon -->
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div data-aos="fade-up" data-aos-duration="1500"
        class="mb-5 container align-items-center  d-flex justify-content-center">
        <div class="w-50 p-4 bg-white box-shadow-custom border-custom">
            <h2 class="text-center">Влизане</h2> <!-- Login header -->
            <form method="post" class="my-4"> <!-- Form for login -->
                <?php
                // Display any result message (e.g., error message) if set
                if (isset($result)) {
                    echo $result; // Output the result message
                }
                $result = ""; // Reset result variable
                ?>
                <!-- Input field for username -->
                <input type="text" name="username" placeholder="Имейл адрес" class="mx-auto mb-2 pe-1 w-100" required>
                <!-- Username input -->
                <!-- Input field for password -->
                <input type="password" name="password" placeholder="Парола" class="mx-auto mb-3 pe-1 w-100" required>
                <!-- Password input -->
                <!-- Submit button for the form -->
                <button type="submit" class="btn-submit px-3 d-flex justify-content-center">Влизане</button>
                <!-- Submit button -->
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script> <!-- Bootstrap JS -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Ionicons ES module -->
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Ionicons fallback -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]') // Select all elements with tooltip
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)) // Initialize Bootstrap tooltips
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <!-- AOS JS for animations -->
    <script>
        AOS.init(); // Initialize AOS animations
    </script>
</body>

</html>