<?php
session_start();
require '../../BACKEND/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders with extended details
$order_stmt = $pdo->prepare("SELECT o.order_id, o.total_amount, o.order_date, o.status, o.delivery_address, 
                                    o.driver_status, d.vehicle_type, d.vehicle_number, u.full_name AS driver_name, 
                                    GROUP_CONCAT(oi.quantity, ' x ', m.name SEPARATOR ', ') AS items
                             FROM orders o
                             INNER JOIN customers c ON o.customer_id = c.customer_id
                             LEFT JOIN delivery_personnel d ON o.driver_id = d.driver_id
                             LEFT JOIN users u ON d.user_id = u.user_id
                             LEFT JOIN order_items oi ON oi.order_id = o.order_id
                             LEFT JOIN meals m ON oi.meal_id = m.meal_id
                             WHERE c.user_id = ? 
                             GROUP BY o.order_id
                             ORDER BY o.order_date DESC");
$order_stmt->execute([$user_id]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Kizuno</title>
    <link rel="stylesheet" href="../css/order_history.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="customer_dashboard.php">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="order_history.php">My Orders</a></li>
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="order-history">
            <h1>Order History</h1>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td>Rs.<?php echo htmlspecialchars($order['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>
                                <button class="details-button" onclick="openModal(<?php echo htmlspecialchars(json_encode($order)); ?>)">View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Modal Structure -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2>Order Details</h2>
            <p><strong>Order ID:</strong> <span id="modalOrderId"></span></p>
            <p><strong>Total Amount:</strong> Rs.<span id="modalTotalAmount"></span></p>
            <p><strong>Order Date:</strong> <span id="modalOrderDate"></span></p>
            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            <p><strong>Delivery Address:</strong> <span id="modalDeliveryAddress"></span></p>
            <p><strong>Driver Name:</strong> <span id="modalDriverName"></span></p>
            <p><strong>Driver Vehicle Type:</strong> <span id="modalVehicleType"></span></p>
            <p><strong>Driver Vehicle Number:</strong> <span id="modalVehicleNumber"></span></p>
            <p><strong>Driver Status:</strong> <span id="modalDriverStatus"></span></p>
            <p><strong>Order Items:</strong> <span id="modalItems"></span></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>

    <script src="../js/order_history.js"></script>
</body>
</html>
