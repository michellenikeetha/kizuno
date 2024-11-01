<?php
session_start();
require '../../BACKEND/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calculate tomorrow's date
$tomorrow = new DateTime('tomorrow');
$tomorrow_date = $tomorrow->format('Y-m-d');

// Fetch meals for tomorrow from the database with optional search, including image_url
if ($search) {
    $stmt = $pdo->prepare("SELECT m.meal_id, m.name, m.description, m.price, m.image_url, u.full_name 
                           FROM meals m 
                           INNER JOIN cooks c ON m.cook_id = c.cook_id
                           INNER JOIN users u ON c.user_id = u.user_id 
                           WHERE (m.name LIKE ? OR m.description LIKE ?) AND m.available_date = ?");
    $stmt->execute(["%$search%", "%$search%", $tomorrow_date]);
} else {
    $stmt = $pdo->prepare("SELECT m.meal_id, m.name, m.description, m.price, m.image_url, u.full_name 
                           FROM meals m 
                           INNER JOIN cooks c ON m.cook_id = c.cook_id
                           INNER JOIN users u ON c.user_id = u.user_id 
                           WHERE m.available_date = ?");
    $stmt->execute([$tomorrow_date]);
}
$meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's orders for tomorrow's date
// $order_stmt = $pdo->prepare("SELECT o.order_id, o.total_amount, o.order_date, o.status , o.driver_status
//                              FROM orders o 
//                              INNER JOIN customers c ON o.customer_id = c.customer_id 
//                              WHERE c.user_id = ? AND o.order_date = ?");
// $order_stmt->execute([$user_id, $tomorrow_date]);
// $orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's orders for tomorrow's date, including driver details if assigned
$order_stmt = $pdo->prepare("SELECT o.order_id, o.total_amount, o.order_date, o.status, o.driver_status,
                             d.vehicle_type, d.vehicle_number, u.full_name AS driver_name, u.phone_number AS driver_phone
                             FROM orders o 
                             INNER JOIN customers c ON o.customer_id = c.customer_id 
                             LEFT JOIN delivery_personnel d ON o.driver_id = d.driver_id
                             LEFT JOIN users u ON d.user_id = u.user_id
                             WHERE c.user_id = ? AND o.order_date = ?");
$order_stmt->execute([$user_id, $tomorrow_date]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kizuno - Customer Dashboard</title>
    <link rel="stylesheet" href="../css/customer_dashboard.css">
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
        <section class="menu">
            <h1>Available Meals for Tomorrow (<?php echo $tomorrow_date; ?>)</h1>
            <form action="customer_dashboard.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search for meals..." required>
                <button type="submit">Search</button>
            </form>

            <?php

                if (isset($_SESSION['error'])) {
                    echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }

                if (isset($_SESSION['success'])) {
                    echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
                    unset($_SESSION['success']);
                }
            ?>

            <div class="meal-list">
                <?php if (!empty($meals)): ?>
                    <?php foreach ($meals as $meal): ?>
                        <div class="meal-item">
                            <?php if ($meal['image_url']): ?>
                                <img src="../RESOURCES/uploads/<?php echo htmlspecialchars($meal['image_url']); ?>" alt="<?php echo htmlspecialchars($meal['name']); ?>" class="meal-image">
                            <?php else: ?>
                                <img src="../RESOURCES/default_meal_image.jpeg" alt="Default Meal Image" class="meal-image">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                            <p><?php echo htmlspecialchars($meal['description']); ?></p>
                            <p>Cook: <?php echo htmlspecialchars($meal['full_name']); ?></p>
                            <p>Price: Rs.<?php echo htmlspecialchars($meal['price']); ?></p>
                            <a href="order.php?meal_id=<?php echo $meal['meal_id']; ?>" class="order-button">Order Now</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No meals available for tomorrow yet. Please check back later!</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="order-tracking">
            <h2>My Recent Orders</h2>
            <?php if (!empty($orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Status - Cook</th>
                            <th>Delivery Status</th>
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
                                    <?php 
                                    if ($order['driver_status'] === 'accepted') {
                                        echo "<button class='view-driver-btn' onclick=\"openDriverModal('" . 
                                        htmlspecialchars($order['driver_name']) . "', '" . 
                                        htmlspecialchars($order['driver_phone']) . "', '" . 
                                        htmlspecialchars($order['vehicle_type']) . "', '" . 
                                        htmlspecialchars($order['vehicle_number']) . "')\">View Driver</button>";
                                    } else {
                                        echo htmlspecialchars($order['driver_status']);
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders-message">You don't have any recent orders yet. Start exploring our meals and place your order today!</p>
            <?php endif; ?>
        </section>

        <!-- Driver Details Modal -->
        <div id="driverModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Driver Details</h2>
                <p><strong>Name:</strong> <span id="driverName"></span></p>
                <p><strong>Phone Number:</strong> <span id="driverPhone"></span></p>
                <p><strong>Vehicle Type:</strong> <span id="vehicleType"></span></p>
                <p><strong>Vehicle Number:</strong> <span id="vehicleNumber"></span></p>
            </div>
        </div>

    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>

    <script>
        function openDriverModal(driverName, driverPhone, vehicleType, vehicleNumber) {
            document.getElementById("driverName").textContent = driverName;
            document.getElementById("driverPhone").textContent = driverPhone;
            document.getElementById("vehicleType").textContent = vehicleType;
            document.getElementById("vehicleNumber").textContent = vehicleNumber;
            document.getElementById("driverModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("driverModal").style.display = "none";
        }

        // Close the modal if user clicks outside it
        window.onclick = function(event) {
            const modal = document.getElementById("driverModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>