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
$refund_quantity = intval($_POST['refund_quantity']);

// Fetch the specific order and product details
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

// Find the index of the product in the order
$product_index = array_search($product_id, $product_ids);

if ($product_index !== false) {
    $quantity = intval($quantities[$product_index]);
    $free_quantity = intval($free_products[$product_index]);
    
    if ($refund_quantity > $quantity) {
        echo "Refund quantity exceeds purchased quantity"; // Debugging statement
        exit();
    }

    // Update the stock back
    $update_sql = "UPDATE Products SET stock = stock + $refund_quantity WHERE product_id = $product_id";
    if ($conn->query($update_sql) === TRUE) {
        echo "Stock updated for product ID: $product_id"; // Debugging statement
    } else {
        echo "Error updating stock: " . $conn->error; // Debugging statement
    }

    // Calculate new quantities after refund
    $new_quantity = $quantity - $refund_quantity;
    $new_free_quantity = floor($new_quantity / $quantity) * $free_quantity; // Adjust free items proportionally

    $quantities[$product_index] = $new_quantity;
    $free_products[$product_index] = $new_free_quantity;

    // If the new quantity is zero, remove the product from the order
    if ($new_quantity == 0) {
        unset($product_ids[$product_index]);
        unset($quantities[$product_index]);
        unset($prices[$product_index]);
        unset($free_products[$product_index]);
    }

    // Update the order in the database
    $update_order_sql = "UPDATE Orders SET 
        product_ids = '" . implode(',', $product_ids) . "', 
        quantities = '" . implode(',', $quantities) . "', 
        prices = '" . implode(',', $prices) . "', 
        free_products = '" . implode(',', $free_products) . "' 
        WHERE order_id = $order_id";
    if ($conn->query($update_order_sql) === TRUE) {
        echo "Order updated successfully"; // Debugging statement
    } else {
        echo "Error updating order: " . $conn->error; // Debugging statement
    }
} else {
    echo "Product not found in the order"; // Debugging statement
}

// Redirect back to order details page
header("Location: order_details.php?order_id=$order_id");
exit();

$conn->close();
?>
