<?php
session_start();
require '../../BACKEND/db.php';  // Include database connection

date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cook') {
    // If not logged in or not a cook, redirect to login page
    header('Location: login.php');
    exit();
}

$cook_id = $_SESSION['user_id'];  // Assuming the cook is logged in
$current_date = new DateTime();
$tomorrow = new DateTime('tomorrow');
$current_time = $current_date->format('H:i');
$cutoff_time = '12:00';

// Fetch menu for today and tomorrow
$stmt_today = $pdo->prepare("SELECT * FROM meals WHERE cook_id = ? AND available_date = ?");
$stmt_today->execute([$cook_id, $current_date->format('Y-m-d')]);
$menu_today = $stmt_today->fetch(PDO::FETCH_ASSOC);

$stmt_tomorrow = $pdo->prepare("SELECT * FROM meals WHERE cook_id = ? AND available_date = ?");
$stmt_tomorrow->execute([$cook_id, $tomorrow->format('Y-m-d')]);
$menu_tomorrow = $stmt_tomorrow->fetch(PDO::FETCH_ASSOC);

// Fetch previous menus
$stmt_previous = $pdo->prepare("SELECT * FROM meals WHERE cook_id = ? AND available_date < ? ORDER BY available_date DESC");
$stmt_previous->execute([$cook_id, $current_date->format('Y-m-d')]);
$previous_menus = $stmt_previous->fetchAll(PDO::FETCH_ASSOC);

// Display appropriate messages
$show_menu_prompt = empty($menu_tomorrow) && ($current_time < $cutoff_time);
?>

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
                    <a href="order_management.php">
                        <img src="../RESOURCES/manage_orders_icon.png" alt="Manage Orders">
                        <p>Manage Orders</p>
                    </a>
                </div>
                <div class="option">
                    <a href="cook_profile.php">
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

            <!-- Display menu for today and tomorrow side by side -->
            <div class="menus-container">
                <div class="menu-section">
                    <h2>Menu for Today (<?php echo $current_date->format('Y-m-d'); ?>):</h2>
                    <?php if (!empty($menu_today)): ?>
                        <div class="menu-details">
                            <p><strong>Meal Name:</strong> <?php echo htmlspecialchars($menu_today['name']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($menu_today['description']); ?></p>
                            <p><strong>Price:</strong> <?php echo htmlspecialchars($menu_today['price']); ?></p>
                            <?php if (!empty($menu_today['image_url'])): ?>
                                <img src="../RESOURCES/uploads/<?php echo htmlspecialchars($menu_today['image_url']); ?>" alt="Meal Image" class="meal-image">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p>No menu uploaded for today.</p>
                    <?php endif; ?>
                </div>

                <div class="menu-section">
                    <h2>Menu for Tomorrow (<?php echo $tomorrow->format('Y-m-d'); ?>):</h2>
                    <?php if (!empty($menu_tomorrow)): ?>
                        <div class="menu-details">
                            <p><strong>Meal Name:</strong> <?php echo htmlspecialchars($menu_tomorrow['name']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($menu_tomorrow['description']); ?></p>
                            <p><strong>Price:</strong> <?php echo htmlspecialchars($menu_tomorrow['price']); ?></p>
                            <?php if (!empty($menu_tomorrow['image_url'])): ?>
                                <img src="../RESOURCES/uploads/<?php echo htmlspecialchars($menu_tomorrow['image_url']); ?>" alt="Meal Image" class="meal-image">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php if ($show_menu_prompt): ?>
                            <p class="warning-message">You haven't uploaded a menu for tomorrow yet. The cutoff time is 12:00 PM today.</p>
                            <a href="menu_upload.php" class="button">Upload Menu Now</a>
                        <?php else: ?>
                            <p>No menu uploaded for tomorrow.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Display previous menus below -->
            <div class="previous-menus-section">
                <h2>Previous Menus:</h2>
                <?php if (!empty($previous_menus)): ?>
                    <div class="previous-menus-grid">
                        <?php foreach ($previous_menus as $previous_menu): ?>
                            <div class="menu-details">
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($previous_menu['available_date']); ?></p>
                                <p><strong>Meal Name:</strong> <?php echo htmlspecialchars($previous_menu['name']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($previous_menu['description']); ?></p>
                                <p><strong>Price:</strong> <?php echo htmlspecialchars($previous_menu['price']); ?></p>
                                <!-- <?php if (!empty($previous_menu['image_url'])): ?>
                                    <img src="../RESOURCES/uploads/<?php echo htmlspecialchars($previous_menu['image_url']); ?>" alt="Meal Image" class="meal-image">
                                <?php endif; ?> -->
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No previous menus available.</p>
                <?php endif; ?>
            </div>
            
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
