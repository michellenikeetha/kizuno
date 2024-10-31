<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); 
require 'db.php';  // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];  // Assuming the cook is logged in
    $meal_name = $_POST['meal_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_date = $_POST['available_date'];

    // Check if it's past 12 PM
    $current_time = new DateTime();
    $cutoff_time = new DateTime('12:00');
    
    if ($current_time >= $cutoff_time) {
        $_SESSION['error'] = "Menu upload is only allowed before 12 PM!";
        header('Location: ../FRONTEND/html/menu_upload.php');
        exit();
    }

    // Fetch cook ID from the cooks table based on the user_id
    $stmt = $pdo->prepare("SELECT cook_id FROM cooks WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cook_id = $stmt->fetchColumn();

    if (!$cook_id) {
        $_SESSION['error'] = "Cook ID not found for the logged-in user.";
        header('Location: ../FRONTEND/html/menu_upload.php');
        exit();
    }

    // Check if a menu already exists for the selected date by this cook
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM meals WHERE cook_id = ? AND available_date = ?");
        $stmt->execute([$cook_id, $available_date]);
        $existing_menu_count = $stmt->fetchColumn();

        if ($existing_menu_count > 0) {
            $_SESSION['error'] = "You have already uploaded a menu for this date!";
            header('Location: ../FRONTEND/html/menu_upload.php');
            exit();
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = time() . '_' . $_FILES['image']['name'];
            $target_dir = "../FRONTEND/RESOURCES/uploads/";
            $target_file = $target_dir . basename($image_name);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        } else {
            $image_name = null;  // If no image uploaded
        }

        // Insert meal details into the database with cook ID
        $stmt = $pdo->prepare("INSERT INTO meals (cook_id, name, description, price, available_date, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cook_id, $meal_name, $description, $price, $available_date, $image_name]);

        // Success message
        $_SESSION['success'] = "Meal successfully uploaded!";
        header('Location: ../FRONTEND/html/cook_dashboard.php');
        exit();
    } catch (PDOException $e) {
        // Error message
        $_SESSION['error'] = "Error uploading meal: " . $e->getMessage();
        header('Location: ../FRONTEND/html/menu_upload.php');
        exit();
    }
}
?>
