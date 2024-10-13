<?php
session_start();
require 'db.php';  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $order_id]);
        $_SESSION['success'] = "Order status updated successfully!";
        header('Location: ../FRONTEND/html/order_management.html');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
        header('Location: ../FRONTEND/html/order_management.html');
        exit();
    }
}
?>
