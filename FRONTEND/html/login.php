<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kizuno - Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        /* Notification Style */
        .notification {
            width: 100%;
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
        <section class="login-section">
            <h1>Login to Kizuno</h1>

            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="notification error">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);  // Clear error after displaying
            }

            if (isset($_SESSION['success'])) {
                echo '<div class="notification success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);  // Clear success after displaying
            }
            ?>

            <form action="../../BACKEND/process_login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
            <p>Don't have an account? <a href="signup_selection.html">Sign Up</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
