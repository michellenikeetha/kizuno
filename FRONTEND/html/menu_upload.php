<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Dashboard - Upload Menu</title>
    <link rel="stylesheet" href="../css/menu_upload.css">
    <script>
        // JavaScript to limit date selection and check time
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const availableDateInput = document.getElementById('available_date');
            
            // Set tomorrow's date as the only valid date
            let tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1);
            const formattedTomorrow = tomorrow.toISOString().split('T')[0];
            
            // Ensure the min and max are set to tomorrow's date
            availableDateInput.setAttribute('min', formattedTomorrow);
            availableDateInput.setAttribute('max', formattedTomorrow);
        });
    </script>

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
        <section class="menu-upload-section">
            <h1>Upload Your Daily Menu</h1>

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

            <form action="../../BACKEND/process_menu_upload.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="name">Meal Name:</label>
                    <input type="text" id="name" name="meal_name" required>
                </div>
                <div class="input-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="input-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="input-group">
                    <label for="available_date">Available Date:</label>
                    <input type="date" id="available_date" name="available_date" required>
                </div>
                <div class="input-group">
                    <label for="image">Meal Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" class="submit-button">Upload Menu</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>
</body>
</html>
