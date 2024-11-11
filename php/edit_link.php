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

<body class="index"> <!-- Body class for styling -->
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-75 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"> <!-- Column for home icon -->
            <a href="../index.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                data-bs-title="Начало" data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover"
                style="color: #fff;"> <!-- Home link -->
                <ion-icon name="home-outline" class="me-1"></ion-icon> <!-- Home icon -->
            </a>
        </div>
        <div class="col-12 col-lg d-flex align-items-center justify-content-center"> <!-- Column for welcome message -->
            <p style="color: white;" class="text-center m-0 welcomeGreeting"> <!-- Welcome message paragraph -->
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
                    style="color: #fff;"> <!-- Registration link -->
                    <ion-icon name="person-circle-outline" class="mt-4 mb-3"></ion-icon> <!-- Registration icon -->
                </a>
            <?php endif ?>
            <?php if (isset($_SESSION['user_id'])): // If user is logged in ?>
                <a href="/php/dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Вашите линкове" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover d-flex align-items-center" style="color: #fff;">
                    <!-- Dashboard link -->
                    <ion-icon name="clipboard-outline" class="me-1"></ion-icon><span class="menu-item"></span>
                    <!-- Dashboard icon -->
                </a>
                <a href="/php/logout.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button"
                    data-bs-title="Изход" data-bs-custom-class="custom-tooltip"
                    class="header_icon menu-item-hover ms-4 d-flex align-items-center" style="color: #fff;">
                    <!-- Logout link -->
                    <ion-icon name="exit-outline" class="me-1"></ion-icon><span class="menu-item"></span>
                    <!-- Logout icon -->
                </a>
            <?php endif; ?>
        </div>
    </div>
    <form data-aos="fade-up" data-aos-duration="1500" class="box-shadow-custom border-custom" method="POST"
        action="/php/edit_link.php?id=<?php echo $linkId; ?>"> <!-- Form for editing link -->
        <div class="p-5 mt-5 text-center"> <!-- Form container -->
            <h1 class="text-center mb-2 ">Редактиране на линк</h1> <!-- Form title -->
            <?php if (isset($result_edit)) {
                echo $result_edit; // Display result message if set
            }
            $result_edit = "" ?>
            <div class="<?php if (isset($disabled)) {
                echo $disabled; // Apply disabled class if set
            }
            $disabled = "" ?>">
                <input type="url" name="original_url" class="mx-auto <?php if (isset($disabled)) {
                    echo $disabled; // Apply disabled class if set
                }
                $disabled = "" ?>" value="<?php echo htmlspecialchars($link['original_url']) ?? null; ?>"
                    placeholder="Нов линк?" required> <!-- Input for new URL -->
                <a href="#additionalOptionsCollapse" data-bs-toggle="collapse"
                    class="d-flex justify-content-center my-3 align-items-center additionalOptions_a"><ion-icon
                        name="chevron-down-circle-outline"></ion-icon> Допълнителни опции</a>
                <!-- Link to additional options -->
                <div class="collapse" id="additionalOptionsCollapse">
                    <!-- Collapsible section for additional options -->
                    <div class="row mb-4 gap-4"> <!-- Row for additional options -->
                        <div class="col-12 col-md justify-content-center"> <!-- Column for expiration date -->
                            <p class="text-center">Дата на изтичане <ion-icon class="ms-1"
                                    name="arrow-down-outline"></ion-icon></p> <!-- Expiration date label -->
                            <div class="d-flex justify-content-center">
                                <input class="w-auto" type="datetime-local"
                                    value="<?php echo htmlspecialchars($link['expires_at']); ?>" name="expires_at">
                                <!-- Input for expiration date -->
                            </div>
                        </div>
                        <div class="col-12 col-md justify-content-center"> <!-- Column for password protection -->
                            <p class="text-center">Защита с парола <ion-icon class="ms-1"
                                    name="arrow-down-outline"></ion-icon></p> <!-- Password protection label -->
                            <div class="d-flex justify-content-center">
                                <input class="w-auto" type="password" name="password"
                                    placeholder="Въведете нова или оставете празно"> <!-- Input for new password -->
                            </div>
                        </div>
                        <div class="col-12 col-md justify-content-center"> <!-- Column for usage limit -->
                            <p class="text-center">Лимит на използване<ion-icon class="ms-1"
                                    name="arrow-down-outline"></ion-icon></p> <!-- Usage limit label -->
                            <div class="d-flex justify-content-center">
                                <input class="w-auto" type="number" name="usage_limit"> <!-- Input for usage limit -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-md-flex mx-auto text-center d-block align-items-center justify-content-center">
                    <!-- Button container -->
                    <button href="" type="submit"
                        class="btn-submit m-0 me-md-3 mx-auto mx-md-0 d-flex align-items-center"><ion-icon
                            name="link-outline" class="me-1"></ion-icon> Записване на промените</button>
                    <!-- Submit button -->
                    <a href="/php/dashboard.php" class="mt-4 mt-md-0"
                        style="color: black; text-decoration:none;">Откажи</a> <!-- Cancel link -->
                </div>
            </div>
        </div>
    </form>
