<?php
session_start();
require '../../BACKEND/db.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$meal_id = $_GET['meal_id'];
$stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
$stmt->execute([$meal_id]);
$meal = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Meal - Kizuno</title>
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
                <li><a href="customer_dashboard.php">Home</a></li>
                <li><a href="order_history.php">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Order <?php echo htmlspecialchars($meal['name']); ?></h1>
        <form action="../../BACKEND/process_order.php" method="POST">
            <input type="hidden" name="meal_id" value="<?php echo $meal['meal_id']; ?>">
            <p>Price: $<?php echo htmlspecialchars($meal['price']); ?></p>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required>
            <label for="delivery_method">Delivery Method:</label>
            <select name="delivery_method" id="delivery_method">
                <option value="delivery">Delivery</option>
                <option value="pickup">Pick-up</option>
            </select>
            <button type="submit">Place Order</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
