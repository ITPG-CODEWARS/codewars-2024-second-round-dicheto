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

<!DOCTYPE html> <!-- Declares the document type and version of HTML -->
<html lang="bg"> <!-- Sets the language of the document to Bulgarian -->

<head>
    <meta charset="UTF-8"> <!-- Specifies the character encoding for the HTML document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Ensures proper rendering on mobile devices -->
    <title>Скъсяване на URL</title> <!-- Sets the title of the webpage -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Links to the external CSS stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Links to Bootstrap CSS for styling -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Links to AOS (Animate On Scroll) CSS for animations -->
    <style>
        @media (max-width:500px) {
            < !-- Media query for responsive design -->.w-50 {
                < !-- Targets elements with class 'w-50' -->width: 75% !important;
                < !-- Sets width to 75% for smaller screens -->
            }
        }
    </style>
</head>

<body class="index"> <!-- Sets the class for the body element -->
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-75 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Creates a flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"><a href="../index.php"
                data-bs-toggle="tooltip" data-bs-placement="right" role="button" data-bs-title="Начало"
                data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover "
                style="color: #fff;"><ion-icon name="home-outline" class="me-1"></ion-icon></a></div>
        <!-- Home icon link with tooltip -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <!-- Flex container for welcome message -->
            <p style="color: white;" class="text-center m-0 welcomeGreeting">
                <?php if (isset($_SESSION['user_id'])) {
                    echo $result_welcomeGreeting . ", " . htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']);
                } else {
                    echo $result_welcomeGreeting . "!";
                } ?>
            </p> <!-- Displays a welcome message based on user session -->
        </div>
        <div class="col-12 col-lg d-flex justify-content-around"> <!-- Flex container for navigation links -->
            <?php if (!isset($_SESSION['user_id'])): ?> <!-- Checks if user is not logged in -->
                <a href="/php/register.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Регистрация" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center justify-content-center"
                    style="color: #fff;"><ion-icon name="person-circle-outline" class="mt-4 mb-3"></ion-icon></a>
                <!-- Registration link with tooltip -->
            <?php endif ?>
            <?php if (isset($_SESSION['user_id'])): ?> <!-- Checks if user is logged in -->
                <a href="/php/dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Вашите линкове" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center" style="color: #fff;"><ion-icon
                        name="clipboard-outline" class="me-1"></ion-icon><span class="menu-item"></span></a>
                <!-- Dashboard link with tooltip -->
                <a href="/php/logout.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Изход" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover ms-4 d-flex align-items-center" style="color: #fff;"><ion-icon
                        name="exit-outline" class="me-1"></ion-icon><span class="menu-item"></span></a>
                <!-- Logout link with tooltip -->
            <?php endif; ?>
        </div>
    </div>
    <div class="my-3 container d-flex justify-content-center"> <!-- Container for the main content -->
        <div data-aos="fade-up" data-aos-duration="1500"
            class="w-50 box-shadow-custom border-custom p-4 bg-white text-center">
            <!-- Box for displaying the shortened link and QR code -->
            <?php if ($result_status): ?> <!-- Checks if there is a result status -->
                <h2>Линкът ви e <?php echo $result_status; ?></h2> <!-- Displays the status of the link -->
            <?php endif; ?>
            <img src="" alt=""> <!-- Placeholder for an image -->
            <p>Това е вашият съкратен линк и QR код за по-лесно ползване:</p> <!-- Instructional text -->
            <a href="<?php echo $shortURL; ?>"><?php echo $shortURL; ?></a> <!-- Displays the shortened URL -->
            <div class="mt-3 mb-2"><img src="<?php echo $qrPath; ?>" class="mb-3" alt="QR Code"></div>
            <!-- Displays the QR code -->
            <a href="<?php echo "$qrPath" ?>" class="btn-submit mt-2" download>Изтегли</a>
            <!-- Download link for the QR code -->
            <p class="mt-4">Ако сте влезли в профила си, ще можете да видите линка.</p>
            <!-- Message about link visibility -->
            <a href="../index.php" class="btn-submit d-flex justify-content-center">Върни ме у дома</a>
            <!-- Link to return home -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@floating-ui/core@1.6.8"></script> <!-- Script for floating UI -->
    <script src="https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.6.12"></script> <!-- Script for floating UI DOM -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script> <!-- Bootstrap JavaScript bundle -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Ionicons ES module -->
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Ionicons fallback for non-module browsers -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]') // Selects all elements with tooltip data attribute
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)) // Initializes Bootstrap tooltips for selected elements
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <!-- Script for AOS functionality -->
    <script>
        AOS.init(); // Initializes AOS for animations on scroll
    </script>
</body>

</html>