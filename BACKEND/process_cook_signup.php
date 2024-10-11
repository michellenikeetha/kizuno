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
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: ../FRONTEND/html/cook_signup.php');
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert user into `users` table
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, user_type) VALUES (?, ?, ?, ?, 'cook')");
        $stmt->execute([$full_name, $email, $phone_number, $password_hash]);

        // Get the last inserted user_id
        $user_id = $pdo->lastInsertId();

        // Insert cook-specific data into `cooks` table
        $stmt = $pdo->prepare("INSERT INTO cooks (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // Set success message and redirect to login page
        $_SESSION['success'] = "Cook registration successful! You can now login.";
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
        header('Location: ../FRONTEND/html/cook_signup.php');
        exit();
    }
}

?>
