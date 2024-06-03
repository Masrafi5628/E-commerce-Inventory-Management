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
$buy_offer = explode(',', $order['buy_offer']);
$get_offer = explode(',', $order['get_offer']);

// Find the index of the product in the order
$product_index = array_search($product_id, $product_ids);
echo "Product Index : $product_index\n"; // Debugging

if ($product_index !== false) {
    $quantity = intval($quantities[$product_index]);
    $free_quantity = intval($free_products[$product_index]);
    $old_free_quantity = $free_quantity; // Assign old free quantity
    echo "Old Free Quantity: $old_free_quantity "; // Debugging
    echo $free_quantity;
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

    // Handle buy_n_get_m offer
    $new_free_quantity = 0;
    if ($buy_offer[$product_index] != 'null' && $get_offer[$product_index] != 'null') {
        $buy_quantity = intval($buy_offer[$product_index]);
        $get_quantity = intval($get_offer[$product_index]);
        
        // $old_free_quantity = $free_quantity; // original free quantity before refund

        // Calculate new free products based on the new quantity after refund
        $new_free_quantity = floor($new_quantity / $buy_quantity) * $get_quantity;
        echo "Old Free Quantity: $old_free_quantity, New Free Quantity: $new_free_quantity";
        // Add the returned free products back to the inventory
        $returned_free_quantity = $old_free_quantity - $new_free_quantity;
        if ($returned_free_quantity > 0) {
            $update_sql = "UPDATE Products SET stock = stock + $returned_free_quantity WHERE product_id = $product_id";
            if ($conn->query($update_sql) === TRUE) {
                echo "Stock updated for returned free products of product ID: $product_id"; // Debugging statement
            } else {
                echo "Error updating stock for free products: " . $conn->error; // Debugging statement
            }
        }
    }

    $quantities[$product_index] = $new_quantity;
    $free_products[$product_index] = $new_free_quantity;

    // If the new quantity is zero, remove the product from the order
    if ($new_quantity == 0) {
        unset($product_ids[$product_index]);
        unset($quantities[$product_index]);
        unset($prices[$product_index]);
        unset($free_products[$product_index]);
        unset($buy_offer[$product_index]);
        unset($get_offer[$product_index]);
    }

    // Update the order in the database
    $update_order_sql = "UPDATE Orders SET 
        product_ids = '" . implode(',', $product_ids) . "', 
        quantities = '" . implode(',', $quantities) . "', 
        prices = '" . implode(',', $prices) . "', 
        free_products = '" . implode(',', $free_products) . "',
        buy_offer = '" . implode(',', $buy_offer) . "',
        get_offer = '" . implode(',', $get_offer) . "'
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
