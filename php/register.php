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
        $_SESSION['first_name'] = $_POST['first_name']; // Store user's first name in session
        $_SESSION['last_name'] = $_POST['last_name']; // Store user's last name in session
        header("Location: ../index.php"); // Redirect to the index page after successful registration
        exit(); // Terminate the script
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Set character encoding for the document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Set viewport for responsive design -->
    <title>Лог-ин</title> <!-- Set the title of the page -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Link to the custom stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- Link to AOS CSS for animations -->
    <style>
        @media (max-width:500px) {
            .w-50 {
                width: 75% !important; // Adjust width for smaller screens
            }
        }
    </style>
</head>

<body class="index">
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-50 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <a href="../index.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
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
        class="mb-5 container align-items-center d-flex justify-content-center">
        <div class="w-50 p-3 p-md-4 box-shadow-custom border-custom bg-white text-center">
            <!-- Container for registration form -->
            <h2 class="text-center mb-0">Регистрация</h2> <!-- Registration title -->
            <p class="mt-0">Имаш регистрация? <a style="text-decoration: underline; color: black;;"
                    href="/php/login.php">Влез</a></p> <!-- Link to login page -->
            <form method="POST" class="my-4"> <!-- Form for user registration -->
                <?php if (isset($error_register)) { // Check if there is an error message
                        echo $error_register; // Display the error message
                    }
                    $error_register = "" ?> <!-- Reset error message -->
                <input type="text" name="first_name" placeholder="Име" class="mx-auto mb-2 w-100 pe-1" required>
                <!-- Input for first name -->
                <input type="text" name="last_name" placeholder="Фамилия" class="mx-auto mb-2 w-100 pe-1" required>
                <!-- Input for last name -->
                <input type="text" name="username" placeholder="Имейл адрес" class="mx-auto mb-2 w-100 pe-1" required>
                <!-- Input for username (email) -->
                <input type="password" name="password" placeholder="Парола" class="mx-auto mb-3 w-100 pe-1" required>
                <!-- Input for password -->

                <button type="submit" class="btn-submit px-3 d-flex justify-content-center">Регистриране</button>
                <!-- Submit button for registration -->
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script> <!-- Bootstrap JS -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Ionicons ES module -->
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Ionicons for non-module browsers -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]') // Select all elements with tooltip
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)) // Initialize Bootstrap tooltips
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init(); // Initialize AOS (Animate On Scroll) library
    </script>
</body>

</html>