<?php include '../../BACKEND/session_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Dashboard - Kizuno</title>
    <link rel="stylesheet" href="../css/cook_dashboard.css">
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
        <section class="dashboard-section">
            <h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
            <div class="dashboard-options">
                <div class="option">
                    <a href="menu_upload.php">
                        <img src="../RESOURCES/upload_menu_icon.png" alt="Upload Menu">
                        <p>Upload Menu</p>
                    </a>
                </div>
                <div class="option">
                    <a href="order_management.html">
                        <img src="../RESOURCES/manage_orders_icon.png" alt="Manage Orders">
                        <p>Manage Orders</p>
                    </a>
                </div>
                <div class="option">
                    <a href="profile.html">
                        <img src="../RESOURCES/profile_icon.png" alt="Profile">
                        <p>View/Edit Profile</p>
                    </a>
                </div>
            </div>

            <!-- Display success or error messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
