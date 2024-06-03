<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$order_sql = "SELECT * FROM Orders WHERE user_id = $user_id ORDER BY order_id DESC";
$order_result = $conn->query($order_sql);

$orders = [];
if ($order_result->num_rows > 0) {
    while ($row = $order_result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    echo "No orders found for user ID: $user_id"; // Debugging statement
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Your Order History</h2>
    <a href="index.php">Continue Shopping</a>
    <?php if (empty($orders)): ?>
        <p>You have no orders.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Names</th>
                    <th>Total Price (৳)</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): 
                    // Fetch product names for this order
                    $product_ids = explode(',', $order['product_ids']);
                    $product_names = [];
                    foreach ($product_ids as $product_id) {
                        $product_sql = "SELECT name FROM Products WHERE product_id = $product_id";
                        $product_result = $conn->query($product_sql);
                        if ($product_result->num_rows > 0) {
                            $product_row = $product_result->fetch_assoc();
                            $product_names[] = $product_row['name'];
                        } else {
                            $product_names[] = "Unknown Product (ID: $product_id)"; // Debugging statement
                        }
                    }
                    ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo implode(', ', $product_names); ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td>
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>">Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
