<?php
session_start();  // Start session to manage success/error messages
require 'db.php';  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];

    // Full name validation: cannot be empty
    if (empty($full_name)) {
        $errors[] = "Full name cannot be empty.";
    }

    // Email validation: cannot be empty and must be a valid email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    // Phone number validation: must be exactly 10 digits
    if (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }
    
    // Password validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If there are any validation errors, redirect back to the signup form with an error message
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: ../FRONTEND/html/customer_signup.php');
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert user into `users` table
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, user_type) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->execute([$full_name, $email, $phone_number, $password_hash]);

        // Get the last inserted user_id
        $user_id = $pdo->lastInsertId();

        // Insert customer-specific data into `customers` table
        $stmt = $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // Set success message and redirect to login page
        $_SESSION['success'] = "Customer registration successful! You can now login.";
        header('Location: ../FRONTEND/html/login.php');
        exit();

    } catch (PDOException $e) {
        // Check if it's a duplicate entry error (SQLSTATE[23000])
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "An account with this email already exists. Please use a different email.";
        } else {
            // Handle any other errors
            $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
        }
        header('Location: ../FRONTEND/html/customer_signup.php');
        exit();
    }
}

?>
