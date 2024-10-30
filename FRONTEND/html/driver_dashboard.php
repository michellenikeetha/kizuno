<?php
session_start();
require '../../BACKEND/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'driver') {
    header('Location: login.php');
    exit();
}

$driver_id = $_SESSION['user_id'];
$errors = $success = '';

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_number = $_POST['vehicle_number'];

    // Update driver profile in delivery_personnel table
    $stmt = $pdo->prepare("UPDATE delivery_personnel SET vehicle_type = ?, vehicle_number = ? WHERE user_id = ?");
    if ($stmt->execute([$vehicle_type, $vehicle_number, $driver_id])) {
        $success = "Profile updated successfully!";
    } else {
        $errors = "Failed to update profile. Try again.";
    }
}

// Fetch profile information
$stmt = $pdo->prepare("SELECT vehicle_type, vehicle_number FROM delivery_personnel WHERE user_id = ?");
$stmt->execute([$driver_id]);
$driver_profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch unassigned orders
$stmt = $pdo->prepare("SELECT o.order_id, o.delivery_address, o.total_amount, o.order_date 
                       FROM orders o
                       WHERE o.driver_id IS NULL AND o.driver_status = 'unassigned'");
                    //    WHERE o.driver_id IS NULL AND o.status = 'pending' AND o.driver_status = 'unassigned'");
$stmt->execute();
$unassigned_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch accepted orders
$stmt = $pdo->prepare("SELECT o.order_id, o.delivery_address, o.total_amount, o.order_date 
                       FROM orders o
                       WHERE o.driver_id = ? AND o.driver_status = 'accepted'");
$stmt->execute([$driver_id]);
$accepted_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Accept an order
if (isset($_GET['accept_order_id'])) {
    $order_id = $_GET['accept_order_id'];
    $stmt = $pdo->prepare("UPDATE orders SET driver_id = ?, driver_status = 'accepted' WHERE order_id = ?");
    $stmt->execute([$driver_id, $order_id]);
    header('Location: driver_dashboard.php');
    exit();
}

// Mark an order as delivered
if (isset($_GET['deliver_order_id'])) {
    $order_id = $_GET['deliver_order_id'];
    $stmt = $pdo->prepare("UPDATE orders SET driver_status = 'delivered', status = 'delivered' WHERE order_id = ?");
    $stmt->execute([$order_id]);
    header('Location: driver_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard - Kizuno</title>
    <link rel="stylesheet" href="../css/driver_dashboard.css">
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
                <!-- <li><a href="previous_menus.php">Menus</a></li>  -->
                <li><a href="../../BACKEND/logout.php">Logout</a></li> 
            </ul>
        </nav>
        
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
                    <a href="driver_profile.php">
                        <img src="../RESOURCES/driver.png" alt="Profile">
                        <p>View/Edit Profile</p>
                    </a>
                </div>
            </div>

            <!-- Profile Update Section -->
            <section>
                <h2>Update Profile</h2>
                <?php if ($errors) echo "<p class='error'>$errors</p>"; ?>
                <?php if ($success) echo "<p class='success'>$success</p>"; ?>
                <form method="POST">
                    <label for="vehicle_type">Vehicle Type:</label>
                    <input type="text" name="vehicle_type" id="vehicle_type" value="<?= htmlspecialchars($driver_profile['vehicle_type'] ?? '') ?>">
                    
                    <label for="vehicle_number">Vehicle Number:</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" value="<?= htmlspecialchars($driver_profile['vehicle_number'] ?? '') ?>">
                    
                    <button type="submit" name="update_profile">Update Profile</button>
                </form>
            </section>

            <!-- Unassigned Orders Section -->
            <section>
                <h2>Available Orders</h2>
                <?php if (empty($unassigned_orders)): ?>
                    <p>No available orders to accept.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($unassigned_orders as $order): ?>
                            <li>
                                <strong>Order ID:</strong> <?= $order['order_id'] ?> | 
                                <strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?> | 
                                <strong>Amount:</strong> Rs.<?= $order['total_amount'] ?> |
                                <a href="?accept_order_id=<?= $order['order_id'] ?>">Accept</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <!-- Accepted Orders Section -->
            <section>
                <h2>Accepted Orders</h2>
                <?php if (empty($accepted_orders)): ?>
                    <p>No orders accepted yet.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($accepted_orders as $order): ?>
                            <li>
                                <strong>Order ID:</strong> <?= $order['order_id'] ?> |
                                <strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?> |
                                <strong>Amount:</strong> Rs.<?= $order['total_amount'] ?> |
                                <a href="?deliver_order_id=<?= $order['order_id'] ?>">Mark as Delivered</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>

</body>
</html>
