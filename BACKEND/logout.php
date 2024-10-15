<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the home page or login page
header('Location: ../FRONTEND/html/login.php');
exit();
?>
