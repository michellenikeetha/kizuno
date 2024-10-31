<?php
session_start();
require 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    // $delivery_method = $_POST['delivery_method'];
    $delivery_address = $_POST['delivery_address'];
    $user_id = $_SESSION['user_id'];

    // Fetch the customer's ID from the customers table based on the logged-in user's ID
    $customer_stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $customer_stmt->execute([$user_id]);
    $customer = $customer_stmt->fetch(PDO::FETCH_ASSOC);

    // Ensure a valid customer is found
    if (!$customer) {
        $_SESSION['error'] = "Customer not found.";
        header('Location: customer_dashboard.php');
        exit();
    }

    $customer_id = $customer['customer_id'];

    // Fetch meal price
    $stmt = $pdo->prepare("SELECT price FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate total amount for the order
    $total_amount = $meal['price'] * $quantity;

    // Insert order into orders table with customer_id
    $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status, delivery_address) VALUES (?, ?, 'pending', ?)");
    $order_stmt->execute([$customer_id, $total_amount, $delivery_address]);

    // Get the new order ID
    $order_id = $pdo->lastInsertId();

    // Insert order item into order_items table with quantity and price per item
    $order_item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, meal_id, quantity, price) VALUES (?, ?, ?, ?)");
    $order_item_stmt->execute([$order_id, $meal_id, $quantity, $meal['price']]);

    $_SESSION['success'] = "Order placed successfully!";
    header('Location: ../FRONTEND/html/customer_dashboard.php');
    exit();
} else {
    header('Location: ../FRONTEND/html/customer_dashboard.php');
    exit();
}
