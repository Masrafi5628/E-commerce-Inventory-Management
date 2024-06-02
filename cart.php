<?php
session_start();
include('config.php');

// Function to calculate the discount for a product
function calculateDiscount($product, $discounts, $quantity) {
    $product_tags = explode(',', $product['tags']);
    foreach ($discounts as $discount) {
        $discount_tags = explode(',', $discount['tags']);
        if (array_intersect($product_tags, $discount_tags)) {
            if ($discount['discount_type'] == 'percentage') {
                return '-' . $discount['discount_value'] . '%';
            } elseif ($discount['discount_type'] == 'buy_n_get_m' && $quantity >= $discount['buy_quantity']) {
                return '+' . $discount['discount_value'] . ' free';
            }
        }
    }
    return '';
}

// Fetch discounts from the database
$discount_sql = "SELECT * FROM Discounts WHERE start_date <= CURDATE() AND end_date >= CURDATE()";
$discount_result = $conn->query($discount_sql);

$discounts = [];
if ($discount_result->num_rows > 0) {
    while ($row = $discount_result->fetch_assoc()) {
        $discounts[] = $row;
    }
}

// Fetch products in the cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Fetch product details
$products = [];
if (count($cart) > 0) {
    $product_ids = implode(',', array_keys($cart));
    $product_sql = "SELECT * FROM Products WHERE product_id IN ($product_ids)";
    $product_result = $conn->query($product_sql);

    if ($product_result->num_rows > 0) {
        while ($row = $product_result->fetch_assoc()) {
            $products[] = $row;
        }
    }
} 

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
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
    <h2>Your Cart</h2>
    <a href="index.php">Continue Shopping</a>
    <?php if (empty($cart)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (৳)</th>
                    <th>Discount</th>
                    <th>Total (৳)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                foreach ($products as $product) {
                    $product_id = $product['product_id'];
                    $quantity = $cart[$product_id];
                    $price = $product['price'];
                    $total = $price * $quantity;
                    $discount = calculateDiscount($product, $discounts, $quantity);
                    $discounted_total = $total;

                    if (strpos($discount, '%') !== false) {
                        $percentage = floatval(str_replace('-', '', str_replace('%', '', $discount)));
                        $discounted_total = $total - ($total * ($percentage / 100));
                    } elseif (strpos($discount, 'free') !== false) {
                        $free_items = intval(str_replace('+', '', str_replace(' free', '', $discount)));
                        $discounted_total = $price * ($quantity - $free_items);
                    }

                    $grand_total += $discounted_total;
                    echo "<tr>
                        <td>" . htmlspecialchars($product['name']) . "</td>
                        <td>$quantity</td>
                        <td>" . number_format($price, 2) . "</td>
                        <td>$discount</td>
                        <td>" . number_format($discounted_total, 2) . "</td>
                    </tr>";
                }
                ?>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>Grand Total</strong></td>
                    <td><strong><?php echo number_format($grand_total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
