<?php
session_start();
require '../../BACKEND/db.php'; // Include the database connection

// Ensure the user is logged in and is a cook
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cook') {
    header('Location: login.php');
    exit();
}

$cook_id = $_SESSION['user_id'];

// Fetch all orders for meals prepared by the cook
$stmt = $pdo->prepare("
    SELECT o.order_id, o.total_amount, o.order_date, o.delivery_address, o.status, u.full_name, u.phone_number
    FROM orders o
    INNER JOIN order_items oi ON o.order_id = oi.order_id
    INNER JOIN meals m ON oi.meal_id = m.meal_id
    INNER JOIN customers c ON o.customer_id = c.customer_id
    INNER JOIN users u ON c.user_id = u.user_id
    WHERE m.cook_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
$stmt->execute([$cook_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update the status of the order
    $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $update_stmt->execute([$status, $order_id]);

    $_SESSION['success'] = "Order status updated successfully.";
    header('Location: order_management.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Kizuno</title>
    <link rel="stylesheet" href="../css/order_management.css">
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
        <section class="order-management-section">
            <h1>Order Management</h1>

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

            <!-- Check if there are no orders -->
            <?php if (empty($orders)): ?>
                <div class="no-orders-message">
                    <img src="../RESOURCES/no-orders.png" alt="No Orders" class="no-orders-icon">
                    <p>No orders have been placed yet. When customers place orders, they will appear here.</p>
                </div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Delivery Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
                                <td class="status-<?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></td>                                <td>
                                    <form action="order_management.php" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="status">
                                            <option value="pending" <?php if ($order['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="accepted" <?php if ($order['status'] === 'accepted') echo 'selected'; ?>>Accepted</option>
                                            <option value="delivered" <?php if ($order['status'] === 'delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="cancelled" <?php if ($order['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>