<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cook') {
    // If not logged in or not a cook, redirect to login page
    header('Location: login.php');
    exit();
}
?>
