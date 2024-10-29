// process_driver_signup.php

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];

    // Full name validation
    if (empty($full_name)) {
        $errors[] = "Full name cannot be empty.";
    }

    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    // Phone number validation
    if (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // Password validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If there are validation errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: ../FRONTEND/html/driver_signup.php');
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert into `users` table with `user_type` set to 'driver'
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, user_type) VALUES (?, ?, ?, ?, 'driver')");
        $stmt->execute([$full_name, $email, $phone_number, $password_hash]);

        // Get the last inserted `user_id`
        $user_id = $pdo->lastInsertId();

        // Insert driver-specific data into `delivery_personnel`
        $stmt = $pdo->prepare("INSERT INTO delivery_personnel (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // Set success message and redirect to login page
        $_SESSION['success'] = "Driver registration successful! You can now login.";
        header('Location: ../FRONTEND/html/login.php');
        exit();

    } catch (PDOException $e) {
        // Handle duplicate entry error or any other error
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "An account with this email already exists. Please use a different email.";
        } else {
            $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
        }
        header('Location: ../FRONTEND/html/driver_signup.php');
        exit();
    }
}
?>
