<?php
// db.php: Database connection configuration

$host = 'localhost';  // Your database host
$dbname = 'kizuno';   // Your database name
$username = 'root';   // Your MySQL username
$password = '';       // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
