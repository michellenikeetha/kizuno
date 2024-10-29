<?php
session_start(); // Start the session to handle error messages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up as Delivery Personnel - Kizuno</title>
    <link rel="stylesheet" href="../css/signup.css">
    <style>
        /* Notification Style */
        .notification {
            /* width: 100%; */
            margin: 0 auto 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            text-align: center;
            font-size: 16px;
        }

        .error {
            background-color: #e74c3c; /* Red for errors */
        }

        .success {
            background-color: #2ecc71; /* Green for success */
        }

        .notification a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.html">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
    </header>

    <main>
        <section class="signup-section">
            <h1>Sign Up as Delivery Personnel</h1>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="notification error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form action="../../BACKEND/process_driver_signup.php" method="POST">
                <div class="input-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="full_name" required>
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone_number" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>
                </div>
                <button type="submit" class="signup-button">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
