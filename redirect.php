<?php
require 'config.php'; // Include the configuration file for database connection
include './php/time.php'; // Include the time-related functions

$shortCode = $_GET['code'] ?? null; // Retrieve the short code from the URL parameters, default to null
$result_title = ""; // Variable for messages
