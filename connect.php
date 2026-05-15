<?php
// connect.php
// This file connects to the database
// We include this file in every page that needs the database

// Connect to MySQL database using mysqli (scripting language connecting to database)
$conn = mysqli_connect("localhost", "root", "", "joes_electronics");

// Check if connection worked
if (!$conn) {
    echo "Connection failed. Please run setup.php first.";
    exit();
}
?>
