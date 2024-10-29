<?php
session_start();
require '../../BACKEND/db.php';  // Include the database connection

// Ensure the user is logged in and is a cook
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cook') {
    header('Location: login.php');
    exit();
}

$cook_id = $_SESSION['user_id'];

// Fetch cook details from the database
$stmt = $pdo->prepare("
    SELECT u.full_name, u.email, u.phone_number, c.bio, c.specialty, c.rating, c.total_reviews 
    FROM users u 
    INNER JOIN cooks c ON u.user_id = c.user_id 
    WHERE u.user_id = ?
");
$stmt->execute([$cook_id]);
$cook = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone_number = $_POST['phone_number'];
    $bio = $_POST['bio'];
    $specialty = $_POST['specialty'];

    // Validate inputs
    $errors = [];

    // Full name validation
    if (empty($full_name)) {
        $errors[] = "Full name cannot be empty.";
    }

    // Phone number validation (only digits and exactly 10 numbers)
    if (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // If no errors, update the profile
    if (empty($errors)) {
        // Update the users table for basic info (without updating email)
        $update_user_stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE user_id = ?");
        $update_user_stmt->execute([$full_name, $phone_number, $cook_id]);

        // Update the cooks table for bio and specialty
        $update_cook_stmt = $pdo->prepare("UPDATE cooks SET bio = ?, specialty = ? WHERE user_id = ?");
        $update_cook_stmt->execute([$bio, $specialty, $cook_id]);

        // Set a success message and refresh the page
        $_SESSION['success'] = "Profile updated successfully.";
        header('Location: cook_profile.php');
        exit();
    } else {
        // Store errors in session to display on the page
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Profile - Kizuno</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/cook_profile.css">
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
        <section class="profile-section">
            <h1>Your Profile</h1>

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

            <!-- Profile form -->
            <form action="cook_profile.php" method="POST">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($cook['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email (cannot be changed):</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cook['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="phone_number"><i class="fas fa-phone"></i> Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($cook['phone_number']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bio"><i class="fas fa-info-circle"></i> Bio:</label>
                    <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($cook['bio']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="specialty"><i class="fas fa-utensils"></i> Specialty:</label>
                    <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($cook['specialty']); ?>">
                </div>

                <!-- <div class="form-group">
                    <label><i class="fas fa-star"></i> Rating:</label>
                    <p><?php echo htmlspecialchars($cook['rating']); ?> / 5.0 (<?php echo htmlspecialchars($cook['total_reviews']); ?> reviews)</p>
                </div> -->

                <button type="submit"><i class="fas fa-save"></i> Update Profile</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
