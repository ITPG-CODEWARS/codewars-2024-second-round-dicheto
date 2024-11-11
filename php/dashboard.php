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
<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8"> <!-- Set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>Моите линкове</title> <!-- Page title -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Link to custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Link to Bootstrap CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- Link to AOS CSS -->
</head>

<body class="index">
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-75 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"> <!-- Column for home icon -->
            <a href="../index.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                data-bs-title="Начало" data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover"
                style="color: #fff;">
                <ion-icon name="home-outline" class="me-1"></ion-icon> <!-- Home icon -->
            </a>
        </div>
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"> <!-- Column for welcome message -->
            <p style="color: white;" class="text-center m-0 welcomeGreeting">
                <?php if (isset($_SESSION['user_id'])) { // Check if user is logged in
                        echo $result_welcomeGreeting . ", " . htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']); // Display welcome message with user's name
                    } else {
                        echo $result_welcomeGreeting . "!"; // Display welcome message for guests
                    } ?>
            </p>
        </div>
        <div class="col-12 col-lg d-flex justify-content-around"> <!-- Column for navigation links -->
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
        class="my-5 dashboard container align-items-center d-flex justify-content-center"> <!-- Dashboard container -->
        <div class="w-75 p-4 bg-white border-custom box-shadow-custom"> <!-- Main content area -->
            <h2 class="text-center">Моите линкове</h2> <!-- Section title -->

            <?php if (isset($deleteMessage)): ?> <!-- Check if delete message is set -->
                <p class="text-center my-3" style="color: green;"><?php echo $deleteMessage; ?></p>
                <!-- Display delete message if set -->
            <?php endif; ?>

            <?php if (count($links) == 0) { ?> <!-- Check if there are no links -->
                <p class="text-center my-3">Нямате линкове свързани с акаунта ви</p> <!-- Message for no links -->
            <?php } ?>

            <?php foreach ($links as $link): ?> <!-- Loop through each link -->
                <li class="dashboard_link-list my-3" style="word-wrap: break-word;"> <!-- List item for each link -->
                    <div class="row"> <!-- Row for link details -->
                        <div class="col-10"> <!-- Column for original URL -->
                            <a href="#info-<?php echo $link['id']; ?>" data-bs-toggle="collapse" role="button"
                                aria-expanded="false"><?php echo htmlspecialchars($link['original_url']); ?></a>
                            <!-- Link to original URL -->
                        </div>
                        <div class="col-1 px-0"> <!-- Column for edit button -->
                            <a href="/php/edit_link.php?id=<?php echo $link['id']; ?>" class="edit-btn me-auto"><ion-icon
                                    name="create-outline"></ion-icon></a> <!-- Edit link icon -->
                        </div>
                        <div class="col-1 px-0"> <!-- Column for delete button -->
                            <a href="/php/dashboard.php?delete_id=<?php echo $link['id']; ?>" class="delete-btn"
                                onclick="return confirm('Сигурни ли сте, че искате да изтриете този линк?');"><ion-icon
                                    name="trash-outline"></ion-icon></a> <!-- Delete link icon -->
                        </div>
                    </div>
                    <div id="info-<?php echo $link['id']; ?>" class="collapse p-3 dashboard_link-listDetails">
                        <!-- Collapsible details for each link -->
                        <p><strong>Дълъг линк:</strong> <?php echo htmlspecialchars($link['original_url']); ?></p>
                        <!-- Display original link -->
                        <p><strong>Кратък код:</strong> <?php echo htmlspecialchars($link['short_code']); ?></p>
                        <!-- Display short code -->
                        <p><strong>Срок за използване:</strong>
                            <?php echo $link['expires_at'] !== "0000-00-00 00:00:00" ? htmlspecialchars($link['expires_at']) : 'Без срок'; ?>
                        </p> <!-- Display expiration date -->
                        <p><strong>Лимит за използване:</strong>
                            <?php echo $link['usage_limit'] ? htmlspecialchars($link['usage_limit']) : 'Без лимит'; ?></p>
                        <!-- Display usage limit -->
                        <p><strong>Парола:</strong> <?php echo $link['password_hash'] ? 'Има' : 'Няма'; ?></p>
                        <!-- Display password status -->
                        <p><strong>Брой кликвания:</strong> <?php echo htmlspecialchars($link['click_count']); ?></p>
                        <!-- Display click count -->
                    </div>
                </li>
            <?php endforeach; ?>

            <a href="../index.php" class="btn-submit mt-4 d-flex justify-content-center" style="color: #fff;">Върни ме у
                дома</a> <!-- Button to return home -->
        </div>
    </div>
