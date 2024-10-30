<?php
session_start();
require '../../BACKEND/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'driver') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch driver_id from delivery_personnel table using user_id from session
$stmt = $pdo->prepare("SELECT driver_id FROM delivery_personnel WHERE user_id = ?");
$stmt->execute([$user_id]);
$driver_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($driver_data) {
    $driver_id = $driver_data['driver_id'];
} else {
    die("Driver not found in the system.");
}

// Fetch delivered orders
$stmt = $pdo->prepare("SELECT o.order_id, o.delivery_address, o.total_amount, o.order_date 
                       FROM orders o
                       WHERE o.driver_id = ? AND o.driver_status = 'delivered'");
$stmt->execute([$driver_id]);
$delivered_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivered Orders - Kizuno</title>
    <link rel="stylesheet" href="../css/driver_dashboard.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="driver_dashboard.php">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="available_orders.php">Available Orders</a></li>
                <li><a href="driver_dashboard.php">Dashboard</a></li>
                <li><a href="driver_profile.php">Profile</a></li>
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="dashboard-section">
            <h1>Delivered Orders</h1>
            <section class="orders-section">
                <?php if (empty($delivered_orders)): ?>
                    <p>No delivered orders yet.</p>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($delivered_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-details">
                                    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                    <p><strong>Amount:</strong> Rs.<?= htmlspecialchars($order['total_amount']) ?></p>
                                    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
