<?php
session_start();
require '../../BACKEND/db.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch meals from the database with optional search
if ($search) {
    $stmt = $pdo->prepare("SELECT m.meal_id, m.name, m.description, m.price, u.full_name 
                            FROM meals m 
                            INNER JOIN users u ON m.cook_id = u.user_id 
                            WHERE m.name LIKE ? OR m.description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT m.meal_id, m.name, m.description, m.price, u.full_name 
                         FROM meals m 
                         INNER JOIN users u ON m.cook_id = u.user_id");
}
$meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's orders for tracking
$order_stmt = $pdo->prepare("SELECT o.order_id, o.total_amount, o.order_date, o.status 
                             FROM orders o 
                             WHERE o.customer_id = ?");
$order_stmt->execute([$user_id]);
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
            <a href="index.html">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="customer_dashboard.php">Home</a></li>
                <li><a href="order_history.php">My Orders</a></li>
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="menu">
            <h1>Available Meals</h1>
            <form action="customer_dashboard.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search for meals..." required>
                <button type="submit">Search</button>
            </form>

            <div class="meal-list">
                <?php foreach ($meals as $meal): ?>
                    <div class="meal-item">
                        <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                        <p><?php echo htmlspecialchars($meal['description']); ?></p>
                        <p>Cook: <?php echo htmlspecialchars($meal['full_name']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars($meal['price']); ?></p>
                        <a href="order.php?meal_id=<?php echo $meal['meal_id']; ?>" class="order-button">Order Now</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="order-tracking">
            <h2>My Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
