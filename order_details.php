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

// Fetch the specific order details
$order_sql = "SELECT * FROM Orders WHERE user_id = $user_id AND order_id = $order_id";
$order_result = $conn->query($order_sql);

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
} else {
    echo "Invalid order ID: $order_id"; // Debugging statement
    exit();
}

$product_ids = explode(',', $order['product_ids']);
$quantities = explode(',', $order['quantities']);
$prices = explode(',', $order['prices']);
$free_products = explode(',', $order['free_products']);
$buy_offer = explode(',', $order['buy_offer']);
$get_offer = explode(',', $order['get_offer']);

// Fetch product details
$products = [];
if (count($product_ids) > 0) {
    $product_ids_imploded = implode(',', $product_ids);
    $product_sql = "SELECT * FROM Products WHERE product_id IN ($product_ids_imploded)";
    $product_result = $conn->query($product_sql);

    if ($product_result->num_rows > 0) {
        while ($row = $product_result->fetch_assoc()) {
            $products[$row['product_id']] = $row;
        }
    }
}
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
    <h2>Order Details</h2>
    <a href="view_orders.php">Back to Order History</a>
    <?php if (empty($product_ids)): ?>
        <p>No products found for this order.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Free Items</th>
                    <th>Price (৳)</th>
                    <th>Discount</th>
                    <th>Total (৳)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                foreach ($product_ids as $index => $product_id) {
                    $product = $products[$product_id];
                    $quantity = intval($quantities[$index]);
                    $price = floatval($prices[$index]);
                    $free_quantity = intval($free_products[$index]);
                    $discounted_total = $price * $quantity;

                    $discount = '';
                    
                    if ($buy_offer[$index] != 'null' && $get_offer[$index] != 'null') {
                        $buy_offer_value = intval($buy_offer[$index]);
                        $get_offer_value = intval($get_offer[$index]);
                        if ($buy_offer_value != 0 && $get_offer_value != 0) {
                            $discount = '+ ' . floor($quantity/$buy_offer_value) *$get_offer_value . ' Free';
                            $discounted_total = $price * ($quantity);
                        }
                    } else {
                        $percentage_discount = floatval($get_offer[$index]);
                        if ($percentage_discount != 0) {
                            $discount = '-' . $percentage_discount . '%';
                            $discounted_total -= $discounted_total * ($percentage_discount / 100);
                        }
                    }

                    $grand_total += $discounted_total;
                    echo "<tr>
                        <td>" . htmlspecialchars($product['name']) . "</td>
                        <td>$quantity</td>
                        <td>$free_quantity</td>
                        <td>" . number_format($price, 2) . "</td>
                        <td>$discount</td>
                        <td>" . number_format($discounted_total, 2) . "</td>
                        <td>
                            <form method='post' action='refund.php'>
                                <input type='hidden' name='order_id' value='$order_id'>
                                <input type='hidden' name='product_id' value='$product_id'>
                                <input type='number' name='refund_quantity' min='1' max='$quantity' value='1' required>
                                <button type='submit'>Refund</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Grand Total</strong></td>
                    <td colspan="2"><strong><?php echo number_format($grand_total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
