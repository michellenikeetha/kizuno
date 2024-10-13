<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Dashboard - Manage Orders</title>
    <link rel="stylesheet" href="../css/cook_dashboard.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="cook_dashboard.php">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
    </header>

    <main>
        <section class="order-management-section">
            <h1>Your Orders</h1>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require '../../BACKEND/db.php';
                    $cook_id = $_SESSION['user_id'];  // Assuming the cook is logged in

                    // $stmt = $pdo->prepare("SELECT o.order_id, u.full_name, o.total_amount, o.status 
                    //                         FROM orders o 
                    //                         JOIN order_items oi ON o.order_id = oi.order_id 
                    //                         JOIN meals m ON oi.meal_id = m.meal_id 
                    //                         JOIN users u ON o.customer_id = u.user_id 
                    //                         WHERE m.cook_id = ?");
                    // $stmt->execute([$cook_id]);

                    // while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //     echo "<tr>
                    //             <td>{$order['order_id']}</td>
                    //             <td>{$order['full_name']}</td>
                    //             <td>{$order['total_amount']}</td>
                    //             <td>{$order['status']}</td>
                    //             <td>
                    //                 <form action='../../BACKEND/update_order_status.php' method='POST'>
                    //                     <input type='hidden' name='order_id' value='{$order['order_id']}'>
                    //                     <select name='status'>
                    //                         <option value='pending'>Pending</option>
                    //                         <option value='delivered'>Delivered</option>
                    //                         <option value='cancelled'>Cancelled</option>
                    //                     </select>
                    //                     <button type='submit'>Update</button>
                    //                 </form>
                    //             </td>
                    //           </tr>";
                    // }

                    // Fetch orders for the next day (linking orders to cook via order_items and meals)
                    $stmt_orders = $pdo->prepare("
                        SELECT o.*, u.full_name, oi.quantity, oi.price
                        FROM orders o
                        JOIN order_items oi ON o.order_id = oi.order_id
                        JOIN meals m ON oi.meal_id = m.meal_id
                        JOIN users u ON o.customer_id = u.user_id
                        WHERE m.cook_id = ? AND o.order_date = ?
                    ");
                    $stmt_orders->execute([$cook_id, $tomorrow->format('Y-m-d')]);
                    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

                    <!-- Display orders for tomorrow -->
                    <div class="orders-section">
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
                    </div>
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
