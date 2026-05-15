<?php
// logout.php
// This file logs the user out and sends them back to login

session_start();
session_destroy(); // Clear all session data (forget who was logged in)
header("Location: login.php");
exit();
?>
