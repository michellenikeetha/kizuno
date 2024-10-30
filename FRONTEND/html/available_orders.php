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

// Fetch unassigned orders
$stmt = $pdo->prepare("SELECT o.order_id, o.delivery_address, o.total_amount, o.order_date 
                       FROM orders o
                       WHERE o.driver_id IS NULL AND o.driver_status = 'unassigned'");
                       //    WHERE o.driver_id IS NULL AND o.status = 'pending' AND o.driver_status = 'unassigned'");
$stmt->execute();
$unassigned_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Accept an order
if (isset($_GET['accept_order_id'])) {
    $order_id = $_GET['accept_order_id'];
    $stmt = $pdo->prepare("UPDATE orders SET driver_id = ?, driver_status = 'accepted' WHERE order_id = ?");
    $stmt->execute([$driver_id, $order_id]);
    header('Location: available_orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Orders - Kizuno</title>
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
                <li><a href="delivered_orders.php">Delivered Orders</a></li>
                <li><a href="driver_dashboard.php">Dashboard</a></li>
                <li><a href="driver_profile.php">Profile</a></li>
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="dashboard-section">
            <h1>Available Orders</h1>
            <section class="orders-section">
                <?php if (empty($unassigned_orders)): ?>
                    <p>No available orders to accept.</p>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($unassigned_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-details">
                                    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                    <p><strong>Amount:</strong> Rs.<?= htmlspecialchars($order['total_amount']) ?></p>
                                    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                                </div>
                                <a href="?accept_order_id=<?= $order['order_id'] ?>" class="accept-button">Accept</a>
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
