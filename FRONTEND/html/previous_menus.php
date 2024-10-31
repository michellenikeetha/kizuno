<?php
session_start();
require '../../BACKEND/db.php';  // Include database connection

date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST

// Check if the user is logged in and is a cook
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cook') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Assuming the cook is logged in

// Fetch cook ID from the cooks table based on the user_id
$stmt = $pdo->prepare("SELECT cook_id FROM cooks WHERE user_id = ?");
$stmt->execute([$user_id]);
$cook_id = $stmt->fetchColumn();

if (!$cook_id) {
    $_SESSION['error'] = "Cook ID not found for the logged-in user.";
    header('Location: login.php');
    exit();
}

// Fetch previous menus
$stmt_previous = $pdo->prepare("SELECT * FROM meals WHERE cook_id = ? AND available_date < ? ORDER BY available_date DESC");
$stmt_previous->execute([$cook_id, date('Y-m-d')]);
$previous_menus = $stmt_previous->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Menus - Kizuno</title>
    <link rel="stylesheet" href="../css/previous_menus.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="cook_dashboard.php">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="menu_upload.php">Upload Menu</a></li>
                <li><a href="order_management.php">Orders</a></li>
                <li><a href="cook_profile.php">Profile</a></li>
                <li><a href="previous_menus.php">Menus</a></li>
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="previous-menus-section">
            <h1>Previous Menus</h1>
            <?php if (!empty($previous_menus)): ?>
                <div class="previous-menus-grid">
                    <?php foreach ($previous_menus as $previous_menu): ?>
                        <div class="menu-details">
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($previous_menu['available_date']); ?></p>
                            <p><strong>Meal Name:</strong> <?php echo htmlspecialchars($previous_menu['name']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($previous_menu['description']); ?></p>
                            <p><strong>Price:</strong> Rs.<?php echo htmlspecialchars($previous_menu['price']); ?></p>
                            <?php if (!empty($previous_menu['image_url'])): ?>
                                <img src="../RESOURCES/uploads/<?php echo htmlspecialchars($previous_menu['image_url']); ?>" alt="Meal Image" class="meal-image">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No previous menus available.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
