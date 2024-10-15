<?php
session_start();
require '../../BACKEND/db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    $delivery_method = $_POST['delivery_method'];
    $user_id = $_SESSION['user_id'];

    // Fetch meal price
    $stmt = $pdo->prepare("SELECT price FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate total amount
    $total_amount = $meal['price'] * $quantity;

    // Insert order into orders table
    $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, delivery_method) VALUES (?, ?, 'pending', ?)");
    $order_stmt->execute([$user_id, $total_amount, $delivery_method]);

    // Get the new order ID
    $order_id = $pdo->lastInsertId();

    // Insert order items into order_items table
    for ($i = 0; $i < $quantity; $i++) {
        $order_item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, meal_id) VALUES (?, ?)");
        $order_item_stmt->execute([$order_id, $meal_id]);
    }

    $_SESSION['success'] = "Order placed successfully!";
    header('Location: customer_dashboard.php');
    exit();
} else {
    header('Location: customer_dashboard.php');
    exit();
}
