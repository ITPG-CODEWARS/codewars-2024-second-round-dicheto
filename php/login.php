<?php


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
