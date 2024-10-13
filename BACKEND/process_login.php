<?php
session_start(); // Start the session to manage user login state
require 'db.php';  // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Fetch user by email
        $stmt = $pdo->prepare("SELECT user_id, full_name, email, password_hash, user_type FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['email'] = $user['email'];

                // Redirect based on user type (cook or customer)
                if ($user['user_type'] == 'cook') {
                    header('Location: ../FRONTEND/html/cook_dashboard.php');
                } else {
                    header('Location: ../FRONTEND/html/customer_dashboard.php');
                }
                exit();
            } else {
                // Invalid password
                $_SESSION['error'] = "Incorrect password.";
                header('Location: ../FRONTEND/html/login.php');
                exit();
            }
        } else {
            // User not found
            $_SESSION['error'] = "No user found with that email address.";
            header('Location: ../FRONTEND/html/login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
        header('Location: ../FRONTEND/html/login.php');
        exit();
    }
}
?>
