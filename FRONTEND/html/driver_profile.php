<?php
session_start();
require '../../BACKEND/db.php';

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'driver') {
    header('Location: login.php');
    exit();
}

$driver_id = $_SESSION['user_id'];
$errors = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_number = $_POST['vehicle_number'];

    // Update driver profile in users and delivery_personnel tables
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE user_id = ?");
    if ($stmt->execute([$full_name, $phone_number, $driver_id])) {
        $stmt2 = $pdo->prepare("UPDATE delivery_personnel SET vehicle_type = ?, vehicle_number = ? WHERE user_id = ?");
        if ($stmt2->execute([$vehicle_type, $vehicle_number, $driver_id])) {
            $success = "Profile updated successfully!";
        } else {
            $errors = "Failed to update vehicle information. Try again.";
        }
    } else {
        $errors = "Failed to update profile. Try again.";
    }
}

// Fetch driver profile details
$stmt = $pdo->prepare("SELECT u.full_name, u.email, u.phone_number, d.vehicle_type, d.vehicle_number 
                       FROM users u 
                       JOIN delivery_personnel d ON u.user_id = d.user_id 
                       WHERE u.user_id = ?");
$stmt->execute([$driver_id]);
$driver_profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Driver Profile - Kizuno</title>
    <link rel="stylesheet" href="../css/driver_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <!-- <li><a href="driver_dashboard.php">Dashboard</a></li> -->
                <li><a href="../../BACKEND/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="profile-edit-section">
            <h1>Edit Profile</h1>
            <?php if ($errors) echo "<p class='error-message'>$errors</p>"; ?>
            <?php if ($success) echo "<p class='success-message'>$success</p>"; ?>

            <form method="POST">
                <label for="full_name"><i class="fas fa-user"></i>Full Name:</label>
                <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($driver_profile['full_name']) ?>" required>

                <label for="email"><i class="fas fa-envelope"></i>Email (cannot be changed):</label>
                <input type="email" id="email" value="<?= htmlspecialchars($driver_profile['email']) ?>" disabled>

                <label for="phone_number"><i class="fas fa-phone"></i>Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" value="<?= htmlspecialchars($driver_profile['phone_number'] ?? '') ?>" required>

                <label for="vehicle_type"><i class="fas fa-car"></i>Vehicle Type:</label>
                <input type="text" name="vehicle_type" id="vehicle_type" value="<?= htmlspecialchars($driver_profile['vehicle_type'] ?? '') ?>">

                <label for="vehicle_number"><i class="fas fa-motorcycle"></i>Vehicle Number:</label>
                <input type="text" name="vehicle_number" id="vehicle_number" value="<?= htmlspecialchars($driver_profile['vehicle_number'] ?? '') ?>">

                <button type="submit" name="update_profile"><i class="fas fa-save"></i> Update Profile</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
