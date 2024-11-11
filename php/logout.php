<?php
// Include the configuration file to access database and other settings
require '../config.php';

// Destroy the current session to log the user out
session_destroy();

// Redirect the user to the index page after logging out
header("Location: ../index.php");

// Terminate the script to ensure no further code is executed
exit();
?>