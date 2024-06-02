<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Fetch order details for the specific order
$order_sql = "SELECT * FROM Orders WHERE user_id = $user_id AND order_id = $order_id";
$order_result = $conn->query($order_sql);

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
} else {
    echo "Invalid order ID.";
    exit();
}

$product_ids = explode(',', $order['product_ids']);
$quantities = explode(',', $order['quantities']);
$prices = explode(',', $order['prices']);
$free_products = explode(',', $order['free_products']);

$products = [];
for ($i = 0; $i < count($product_ids); $i++) {
    $product_sql = "SELECT * FROM Products WHERE product_id = " . $product_ids[$i];
    $product_result = $conn->query($product_sql);
    if ($product_result->num_rows > 0) {
        $products[] = array_merge($product_result->fetch_assoc(), [
            'quantity' => $quantities[$i],
            'price' => $prices[$i],
            'free_quantity' => $free_products[$i]
        ]);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
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
    <h2>Order Details for Order ID: <?php echo $order_id; ?></h2>
    <a href="view_orders.php">Back to Order History</a>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (৳)</th>
                <th>Free Items</th>
                <th>Total Price (৳)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['free_quantity']; ?></td>
                    <td><?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
                    <td>
                        <form method="post" action="refund.php">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" name="refund">Refund</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
