<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); 
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
        header('Location: ../FRONTEND/html/customer_dashboard.php');
        exit();
    }

    $customer_id = $customer['customer_id'];

    // Fetch meal details, including the price and available date
    $stmt = $pdo->prepare("SELECT price, available_date FROM meals WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meal) {
        $_SESSION['error'] = "Meal not found.";
        header('Location: ../FRONTEND/html/customer_dashboard.php');
        exit();
    }

    // Calculate the total amount including delivery cost
    $meal_total = $meal['price'] * $quantity;
    $delivery_cost = 200;
    $total_amount = $meal_total + $delivery_cost;
    $available_date = $meal['available_date'];

    // Time restriction: Allow orders only before 6 PM
    $current_time = new DateTime();
    $cutoff_time = new DateTime('18:00');

    if ($current_time >= $cutoff_time) {
        $_SESSION['error'] = "Orders are only accepted before 6 PM.";
        header('Location: ../FRONTEND/html/customer_dashboard.php');
        exit();
    }

    // Insert order into orders table with customer_id and available date as order_date
    $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, order_date, delivery_address, status) VALUES (?, ?, ?, ?, 'pending')");
    $order_stmt->execute([$customer_id, $total_amount, $available_date, $delivery_address]);

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

?>