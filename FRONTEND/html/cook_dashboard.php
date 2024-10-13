<?php
session_start();
require '../../BACKEND/db.php';  // Include database connection

date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST

$cook_id = $_SESSION['user_id'];  // Assuming the cook is logged in
$current_date = new DateTime();
$tomorrow = new DateTime('tomorrow');
$current_time = $current_date->format('H:i');
$cutoff_time = '12:00';

// Fetch menu for the next day
$stmt = $pdo->prepare("SELECT * FROM meals WHERE cook_id = ? AND available_date = ?");
$stmt->execute([$cook_id, $tomorrow->format('Y-m-d')]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

// // Fetch orders for the next day (linking orders to cook via order_items and meals)
// $stmt_orders = $pdo->prepare("
//     SELECT o.*, u.full_name, oi.quantity, oi.price
//     FROM orders o
//     JOIN order_items oi ON o.order_id = oi.order_id
//     JOIN meals m ON oi.meal_id = m.meal_id
//     JOIN users u ON o.customer_id = u.user_id
//     WHERE m.cook_id = ? AND o.order_date = ?
// ");
// $stmt_orders->execute([$cook_id, $tomorrow->format('Y-m-d')]);
// $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Display appropriate messages
$show_menu_prompt = empty($menu) && ($current_time < $cutoff_time);
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

            <!-- Display menu for tomorrow -->
            <div class="menu-section">
                <h2>Menu for <?php echo $tomorrow->format('Y-m-d'); ?>:</h2>
                <?php if (!empty($menu)): ?>
                    <div class="menu-details">
                        <p><strong>Meal Name:</strong> <?php echo htmlspecialchars($menu['name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($menu['description']); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($menu['price']); ?></p>
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

            <!-- Display orders for tomorrow -->
            <!-- <div class="orders-section">
                <h2>Orders for <?php echo $tomorrow->format('Y-m-d'); ?>:</h2>
                <?php if (!empty($orders)): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Quantity</th>
                                <th>Order Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_time']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders for tomorrow yet.</p>
                <?php endif; ?>
            </div> -->
            
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
