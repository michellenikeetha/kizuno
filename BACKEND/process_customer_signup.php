<?php
require 'db.php';  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
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

        //echo "Customer registration successful!";
        // Redirect or show success page
        header('Location: ../FRONTEND/html/login.html');

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
