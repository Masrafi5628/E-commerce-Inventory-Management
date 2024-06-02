<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'];
$product_id = $_POST['product_id'];

// Fetch the specific order and product details
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

// Find the index of the product in the order
$product_index = array_search($product_id, $product_ids);

if ($product_index !== false) {
    $quantity = $quantities[$product_index];
    $free_quantity = $free_products[$product_index];
    // Update the stock back
    $update_sql = "UPDATE Products SET stock = stock + $quantity + $free_quantity WHERE product_id = $product_id";
    $conn->query($update_sql);

    // Remove the product from the order
    unset($product_ids[$product_index]);
    unset($quantities[$product_index]);
    unset($prices[$product_index]);
    unset($free_products[$product_index]);

    // Update the order in the database
    $updated_product_ids = implode(',', $product_ids);
    $updated_quantities = implode(',', $quantities);
    $updated_prices = implode(',', $prices);
    $updated_free_products = implode(',', $free_products);
    $total_price = array_sum($prices);

    $update_order_sql = "UPDATE Orders SET product_ids = '$updated_product_ids', quantities = '$updated_quantities', prices = '$updated_prices', free_products = '$updated_free_products', total_price = $total_price WHERE order_id = $order_id";
    $conn->query($update_order_sql);

    echo "Product refunded successfully.";
} else {
    echo "Product not found in the order.";
}

$conn->close();
?>
