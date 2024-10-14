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
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $bio = $_POST['bio'];
    $specialty = $_POST['specialty'];

    // Update the users table for basic info
    $update_user_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?");
    $update_user_stmt->execute([$full_name, $email, $phone_number, $cook_id]);

    // Update the cooks table for bio and specialty
    $update_cook_stmt = $pdo->prepare("UPDATE cooks SET bio = ?, specialty = ? WHERE user_id = ?");
    $update_cook_stmt->execute([$bio, $specialty, $cook_id]);

    // Set a success message and refresh the page
    $_SESSION['success'] = "Profile updated successfully.";
    header('Location: cook_profile.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Profile - Kizuno</title>
    <link rel="stylesheet" href="../css/cook_profile.css">
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
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($cook['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cook['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($cook['phone_number']); ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Bio:</label>
                    <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($cook['bio']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="specialty">Specialty:</label>
                    <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($cook['specialty']); ?>">
                </div>

                <div class="form-group">
                    <label>Rating:</label>
                    <p><?php echo htmlspecialchars($cook['rating']); ?> / 5.0 (<?php echo htmlspecialchars($cook['total_reviews']); ?> reviews)</p>
                </div>

                <button type="submit">Update Profile</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
