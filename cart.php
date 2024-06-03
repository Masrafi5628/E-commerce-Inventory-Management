<?php
session_start();
include('config.php');

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
            $products[$row['product_id']] = $row;
        }
    }
}

// Function to calculate the discount for a product
function calculateDiscount($product, $discounts, $quantity, &$buy_offer, &$get_offer) {
    $free_products = 0;
    $product_tags = explode(',', $product['tags']);
    foreach ($discounts as $discount) {
        $discount_tags = explode(',', $discount['tags']);
        if (array_intersect($product_tags, $discount_tags)) {
            if ($discount['discount_type'] == 'percentage') {
                $buy_offer = 'null';
                $get_offer = $discount['discount_value'] . '%';
                return '-' . $discount['discount_value'] . '%';
            } elseif ($discount['discount_type'] == 'buy_n_get_m' && $quantity >= $discount['buy_quantity']) {
                $free_products = floor($quantity / $discount['buy_quantity']) * $discount['discount_value'];
                $buy_offer = $discount['buy_quantity'];
                $get_offer = $discount['discount_value'];
                return '+' . $free_products . ' free';
            }
        }
    }
    $buy_offer = 'null';
    $get_offer = 'null';
    return '';
}

// Handle order confirmation
if (isset($_POST['confirm_order'])) {
    // Store order information in the database
    $user_id = $_SESSION['user_id']; // Assuming you have a user session
    $product_ids = implode(',', array_keys($cart));
    $quantities = implode(',', $cart);
    $prices = [];
    $buy_offers = [];
    $get_offers = [];
    foreach ($cart as $product_id => $quantity) {
        $product = $products[$product_id];
        $prices[] = $product['price'];
        $offer = calculateDiscount($product, $discounts, $quantity, $buy_offer, $get_offer);
        $buy_offers[] = $buy_offer;
        $get_offers[] = $get_offer;
    }
    $total_price = array_sum($prices);

    $order_sql = "INSERT INTO Orders (user_id, product_ids, quantities, prices, total_price, buy_offer, get_offer) VALUES ($user_id, '$product_ids', '$quantities', '" . implode(',', $prices) . "', $total_price, '" . implode(',', $buy_offers) . "', '" . implode(',', $get_offers) . "')";
    $conn->query($order_sql);

    // Update stock quantities for purchased products
    foreach ($cart as $product_id => $quantity) {
        // Subtract purchased quantity from stock
        $update_sql = "UPDATE Products SET stock = stock - $quantity WHERE product_id = $product_id";
        $conn->query($update_sql);

        // Handle free products from discounts
        $product_discounts = calculateDiscount($products[$product_id], $discounts, $quantity, $buy_offer, $get_offer);
        if (strpos($product_discounts, 'free') !== false) {
            $free_products = intval(str_replace('+', '', str_replace(' free', '', $product_discounts)));
            $update_sql = "UPDATE Products SET stock = stock - $free_products WHERE product_id = $product_id";
            $conn->query($update_sql);
        }
    }

    // Clear the cart after confirming the order
    unset($_SESSION['cart']);
    // Redirect to a thank you page or order history
    header('Location: index.php');
    exit();
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
        <form method="post">
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
                    foreach ($cart as $product_id => $quantity) {
                        $product = $products[$product_id];
                        $price = $product['price'];
                        $total = $price * $quantity;
                        $discount = calculateDiscount($product, $discounts, $quantity, $buy_offer, $get_offer);
                        $discounted_total = $total;

                        if (strpos($discount, '%') !== false) {
                            $percentage = floatval(str_replace('-', '', str_replace('%', '', $discount)));
                            $discounted_total = $total - ($total * ($percentage / 100));
                        } elseif (strpos($discount, 'free') !== false) {
                            $discounted_total = $total; // Subtract the value of free products from the total
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
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <button type="submit" name="confirm_order">Confirm Order</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</body>
</html>
