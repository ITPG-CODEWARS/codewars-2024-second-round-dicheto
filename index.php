<?php require './config.php'; // Include the configuration file for database connection
include './php/time.php'; // Include the time-related functions
?>
<?php include './php/time.php'; // Include the time-related functions again ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Set the character encoding for the document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design for mobile devices -->
    <title>URL shortener</title> <!-- Title of the webpage -->
    <link rel="stylesheet" href="/css/style.css"> <!-- Link to the custom stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Link to Bootstrap CSS -->

</head>

<body class="index"> <!-- Body class for styling -->
    <div data-aos="fade-up" data-aos-duration="1500"
        class="d-flex w-75 my-5 mx-auto box-shadow-custom border-custom justify-content-end row">
        <!-- Flex container for layout -->
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <a href="index.php" data-bs-toggle="tooltip" data-bs-placement="right" role="button" data-bs-title="Начало"
                data-bs-custom-class="custom-tooltip"
                class="header_icon d-flex align-items-center justify-content-center menu-item-hover"
                style="color: #fff;">
                <ion-icon name="home-outline" class="me-1"></ion-icon> <!-- Home icon -->
            </a>
        </div>
        <div class="col-12 col-lg d-flex align-items-center justify-content-center">
            <p style="color: white;" class="text-center m-0 welcomeGreeting">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION('first_name'))) { // Check if user is logged in
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
    <form data-aos="fade-up" data-aos-duration="1500" method="POST" action="/php/shorten.php"
        class="box-shadow-custom border-custom"> <!-- Form for URL shortening -->
        <div class="p-3 p-md-5 text-center"> <!-- Centered content -->
            <h1 class="text-center mb-0">💙 URL Shortener 💚</h1> <!-- Main heading -->
            <p class="text-center">Винаги може да е по-късо</p> <!-- Subtitle -->
            <input type="url" name="url"
                class="mx-auto mx-lg-auto w-75 justify-content-center d-flex text-center p-0 px-1"
                placeholder="Какво искате да съкратим? (дълъг линк)" required> <!-- Input for URL -->
            <a href="#additionalOptionsCollapse" data-bs-toggle="collapse"
                class="d-flex justify-content-center my-3 align-items-center additionalOptions_a">
                <ion-icon name="chevron-down-circle-outline"></ion-icon> Допълнителни опции
                <!-- Link to additional options -->
            </a>
            <div class="collapse" id="additionalOptionsCollapse"> <!-- Collapsible section for additional options -->
                <div class="row mb-4 gap-4">
                    <div class="col-12 col-md justify-content-center">
                        <p class="text-center d-flex justify-content-center align-items-center">Дата на изтичане
                            <ion-icon class="ms-1" name="arrow-down-outline"></ion-icon>
                        </p>
                        <div class="d-flex justify-content-center">
                            <input class="w-auto" type="datetime-local" name="expires_at">
                            <!-- Input for expiration date -->
                        </div>
                    </div>
                    <div class="col-12 col-md justify-content-center">
                        <p class="text-center d-flex justify-content-center align-items-center">Защита с парола
                            <ion-icon class="ms-1" name="arrow-down-outline"></ion-icon>
                        </p>
                        <div class="d-flex justify-content-center">
                            <input class="w-auto" type="password" name="password" placeholder="Парола">
                            <!-- Input for password protection -->
                        </div>
                    </div>
                    <div class="col-12 col-md justify-content-center">
                        <p class="text-center d-flex justify-content-center align-items-center">Лимит на използване
                            <ion-icon class="ms-1" name="arrow-down-outline"></ion-icon>
                        </p>
                        <div class="d-flex justify-content-center">
                            <input class="w-auto" type="number" name="usage_limit" placeholder="Лимит на цъкане">
                            <!-- Input for usage limit -->
                        </div>
                    </div>
                </div>
                <div class="row gap-4">
                    <div class="col-12 col-md justify-content-center">
                        <p class="text-center d-flex justify-content-center align-items-center">Дължина на генерирания
                            код <ion-icon class="ms-1" name="arrow-down-outline"></ion-icon></p>
                        <div class="d-flex justify-content-center">
                            <input class="w-auto" id="code_length" type="number" placeholder="Дължина на късия код"
                                name="code_length" min="1" oninput="toggleCodeLength()"> <!-- Input for code length -->
                        </div>
                    </div>
                    <div class="col-12 col-md justify-content-center">
                        <p class="text-center d-flex justify-content-center align-items-center">Персонализиран код
                            <ion-icon class="ms-1" name="arrow-down-outline"></ion-icon>
                        </p>
                        <div class="d-flex justify-content-center">
                            <input class="w-auto" id="custom_code" type="text" name="custom_code"
                                placeholder="Твой къс код" oninput="toggleCustomCode()"> <!-- Input for custom code -->
                        </div>
                    </div>
                </div>
            </div>
            <button href="" type="submit" class="btn-submit my-2 d-flex align-items-center"><ion-icon
                    name="link-outline" class="me-1"></ion-icon> Съкращаване</button> <!-- Submit button -->
        </div>
    </form>
    <div data-aos="fade-up" data-aos-duration="2500"
        class="container bg-white border-custom box-shadow-custom d-flex text-center justify-content-center w-75 my-5 p-3">
        <h4>"В свят, където времето и мястото са ограничени, краткият път често води до най-голямо въздействие."</h4>
        <!-- Inspirational quote -->
    </div>
    <script>
        input.setAttribute('size', input.getAttribute('placeholder').length); // Set input size based on placeholder length
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@floating-ui/core@1.6.8"></script> <!-- Floating UI core script -->
    <script src="https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.6.12"></script> <!-- Floating UI DOM script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script> <!-- Bootstrap JS bundle -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- Ionicons ES module -->
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Ionicons fallback for non-module browsers -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]') // Select all elements with tooltip
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)) // Initialize Bootstrap tooltips
    </script>
    <script>
        // Find the custom code and code length input fields
        const customCodeInput = document.getElementById('custom_code');
        const codeLengthInput = document.getElementById('code_length');

        // Add event listeners for `input` event to check the value of fields on change
        customCodeInput.addEventListener('input', function () {
            // If the custom code field is filled, disable the code length field
            codeLengthInput.disabled = customCodeInput.value.length > 0;
        });

        codeLengthInput.addEventListener('input', function () {
            // If the code length field is filled, disable the custom code field
            customCodeInput.disabled = codeLengthInput.value > 0;
        });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>