<?php
session_start();
require '../../BACKEND/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'driver') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = $success = '';

// Fetch driver_id from delivery_personnel table using user_id from session
$stmt = $pdo->prepare("SELECT driver_id FROM delivery_personnel WHERE user_id = ?");
$stmt->execute([$user_id]);
$driver_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($driver_data) {
    $driver_id = $driver_data['driver_id'];
} else {
    die("Driver not found in the system.");
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $phone_number = $_POST['phone_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_number = $_POST['vehicle_number'];

    // Update phone number in users table
    $stmt = $pdo->prepare("UPDATE users SET phone_number = ? WHERE user_id = ?");
    $stmt->execute([$phone_number, $user_id]);

    // Update vehicle type and number in delivery_personnel table
    $stmt = $pdo->prepare("UPDATE delivery_personnel SET vehicle_type = ?, vehicle_number = ? WHERE user_id = ?");
    if ($stmt->execute([$vehicle_type, $vehicle_number, $user_id])) {
        $success = "Profile updated successfully!";
    } else {
        $errors = "Failed to update profile. Try again.";
    }
}

// Fetch profile information
$stmt = $pdo->prepare("SELECT u.phone_number, d.vehicle_type, d.vehicle_number 
                       FROM users u
                       JOIN delivery_personnel d ON u.user_id = d.user_id 
                       WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$driver_profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch count of unassigned orders for the notification badge
$stmt = $pdo->prepare("SELECT COUNT(*) AS unassigned_count FROM orders WHERE driver_id IS NULL AND status = 'accepted' AND driver_status = 'unassigned'");
$stmt->execute();
$unassigned_count = $stmt->fetchColumn();

// Fetch unassigned orders
$stmt = $pdo->prepare("SELECT o.order_id, o.delivery_address, o.total_amount, o.order_date 
                       FROM orders o
                       WHERE o.driver_id IS NULL AND o.status = 'accepted' AND o.driver_status = 'unassigned'");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <a href="available_orders.php">
                        <img src="../RESOURCES/order-delivery.png" alt="Available Orders">
                        <p>Available Orders</p>
                        <?php if ($unassigned_count > 0): ?>
                            <span class="notification-badge"><?= $unassigned_count ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="option">
                    <a href="delivered_orders.php">
                        <img src="../RESOURCES/manage_orders_icon.png" alt="Delivered Orders">
                        <p>Delivered Orders</p>
                    </a>
                </div>
                <div class="option">
                    <a href="driver_profile.php">
                        <img src="../RESOURCES/driver.png" alt="Profile">
                        <p>View/Edit Profile</p>
                    </a>
                </div>
            </div>

            <!-- Ride Update Section -->
            <section class="ride-update-section">
                <h2>Update Ride Details</h2>
                <?php if ($errors) echo "<p class='error-message'>$errors</p>"; ?>
                <?php if ($success) echo "<p class='success-message'>$success</p>"; ?>
                <form method="POST" class="ride-update-form">
                    <div class="form-group">
                        <label for="phone_number"><i class="fas fa-phone"></i> Phone Number:</label>
                        <input type="text" name="phone_number" id="phone_number" value="<?= htmlspecialchars($driver_profile['phone_number'] ?? '') ?>" >
                    </div>

                    <div class="form-group">
                        <label for="vehicle_type"><i class="fas fa-car"></i> Vehicle Type:</label>
                        <input type="text" name="vehicle_type" id="vehicle_type" value="<?= htmlspecialchars($driver_profile['vehicle_type'] ?? '') ?>">
                    </div>
                        
                    <div class="form-group">
                        <label for="vehicle_number"><i class="fas fa-motorcycle"></i> Vehicle Number:</label>
                        <input type="text" name="vehicle_number" id="vehicle_number" value="<?= htmlspecialchars($driver_profile['vehicle_number'] ?? '') ?>">
                    </div>

                    <button type="submit" name="update_profile"><i class="fas fa-save"></i> Update Profile</button>
                </form>
            </section>

            <!-- Accepted Orders Section -->
            <section class="orders-section">
                <h2>Accepted Orders</h2>
                <?php if (empty($accepted_orders)): ?>
                    <p>No orders accepted yet.</p>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($accepted_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-details">
                                    <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
                                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                    <p><strong>Amount:</strong> Rs.<?= $order['total_amount'] ?></p>
                                </div>
                                <a href="?deliver_order_id=<?= $order['order_id'] ?>" class="deliver-button">Mark as Delivered</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Unassigned Orders Section -->
            <section class="orders-section">
                <h2>Available Orders</h2>
                <?php if (empty($unassigned_orders)): ?>
                    <p>No available orders to accept.</p>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($unassigned_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-details">
                                    <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
                                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                    <p><strong>Amount:</strong> Rs.<?= $order['total_amount'] ?></p>
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
