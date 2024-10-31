<?php
session_start();
require '../../BACKEND/db.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$meal_id = $_GET['meal_id'];

// Fetch the meal details
$stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
$stmt->execute([$meal_id]);
$meal = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the customer's address
$customer_stmt = $pdo->prepare("SELECT address FROM customers WHERE user_id = ?");
$customer_stmt->execute([$user_id]);
$customer = $customer_stmt->fetch(PDO::FETCH_ASSOC);
$customer_address = $customer ? $customer['address'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Meal - Kizuno</title>
    <link rel="stylesheet" href="../css/order_meal.css">
    <script>
        function toggleAddressField() {
            const deliveryMethod = document.getElementById('delivery_method').value;
            const addressField = document.getElementById('delivery_address_field');
            if (deliveryMethod === 'delivery') {
                addressField.style.display = 'block';
            } else {
                addressField.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <a href="customer_dashboard.php">
                <img src="../RESOURCES/logo.png" alt="Kizuno Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="customer_dashboard.php">Home</a></li>
                <li><a href="order_history.php">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Order <?php echo htmlspecialchars($meal['name']); ?></h1>
        <form action="../../BACKEND/process_order.php" method="POST">
            <input type="hidden" name="meal_id" value="<?php echo $meal['meal_id']; ?>">
            <p>Price: Rs.<?php echo htmlspecialchars($meal['price']); ?></p>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required>

            <!-- <label for="delivery_method">Delivery Method:</label>
            <select name="delivery_method" id="delivery_method" onchange="toggleAddressField()" required>
                <option value="">Select Delivery Method</option>
                <option value="pickup">Pick-up</option>
                <option value="delivery">Delivery</option>
            </select> -->

            <!-- Address Field for Delivery -->
            <!-- <div id="delivery_address_field" style="display: none;">
                <label for="delivery_address">Delivery Address:</label>
                <input type="text" name="delivery_address" id="delivery_address" value="<?php echo htmlspecialchars($customer_address); ?>" placeholder="Enter your delivery address" required>
            </div> -->

            <div id="delivery_address_field">
                <label for="delivery_address">Delivery Address:</label>
                <input type="text" name="delivery_address" id="delivery_address" value="<?php echo htmlspecialchars($customer_address); ?>" placeholder="Enter your delivery address" required>
            </div>

            <button type="submit">Place Order</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Kizuno. All rights reserved.</p>
    </footer>

    <script>
        const quantityInput = document.getElementById('quantity');
        const priceElement = document.querySelector('p');
        const basePrice = parseFloat(<?php echo $meal['price']; ?>);

        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value);
            const totalPrice = (basePrice * quantity).toFixed(2);
            priceElement.textContent = `Total Price: Rs.${totalPrice}`;
        }

        quantityInput.addEventListener('input', updateTotalPrice);

        // document.querySelector('form').addEventListener('submit', function(e) {
        //     const deliveryMethod = document.getElementById('delivery_method').value;
        //     const addressField = document.getElementById('delivery_address');
            
        //     if (deliveryMethod === 'delivery' && addressField.value.trim() === '') {
        //         e.preventDefault();
        //         alert('Please enter a delivery address.');
        //     }
        // });

        // Initial price update
        updateTotalPrice();
    </script>

</body>
</html>
