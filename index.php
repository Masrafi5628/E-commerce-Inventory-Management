<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config.php');

// Fetch user information
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'];
$name = $_SESSION['name'];

// Fetch products from the database
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch active discounts from the database
$discount_sql = "SELECT * FROM Discounts WHERE start_date <= CURDATE() AND end_date >= CURDATE()";
$discount_result = $conn->query($discount_sql);

$discounts = [];
if ($discount_result->num_rows > 0) {
    while ($row = $discount_result->fetch_assoc()) {
        $discounts[] = $row;
    }
}

$conn->close();

// Function to get applicable discounts for a product
function getDiscountsForProduct($product, $discounts) {
    $product_tags = explode(',', $product['tags']);
    $applicable_discounts = [];

    foreach ($discounts as $discount) {
        $discount_tags = explode(',', $discount['tags']);
        if (array_intersect($product_tags, $discount_tags)) {
            $applicable_discounts[] = $discount;
        }
    }

    return $applicable_discounts;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h2, h3 {
            color: #333;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .header, .footer {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .product {
            border: 1px solid #ccc;
            background: #fff;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product h4 {
            margin: 10px 0;
            color: #333;
        }
        .product p {
            color: #666;
        }
        .product form {
            margin-top: 10px;
        }
        .product form input[type="number"] {
            width: 50px;
            margin-right: 10px;
        }
        .product form button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .product form button:hover {
            background-color: #2980b9;
        }
        .admin-section {
            border: 1px solid #ccc;
            background: #fff;
            padding: 10px;
            margin: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($is_admin ? 'Admin' : $name); ?></h2>
            <p><a href="logout.php">Logout</a> | <a href="cart.php">View Cart</a></p>
        </div>
    </div>

    <div class="container">
        <?php if ($is_admin): ?>
            <div class="admin-section">
                <h3>Admin Section</h3>
                <p><a href="add_product.php">Add Product</a></p>
                <p><a href="manage_discounts.php">Manage Discounts</a></p>
            </div>
        <?php endif; ?>

        <h3>Product Listings</h3>
        <div class="products">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p>Price: <b>৳ </b><?php echo number_format($product['price'], 2); ?></p>

                        <!-- Display applicable discounts -->
                        <?php
                        $applicable_discounts = getDiscountsForProduct($product, $discounts);
                        if (!empty($applicable_discounts)): ?>
                            <ul>
                                <?php foreach ($applicable_discounts as $discount): ?>
                                    <div color="red">
                                        <?php
                                        if ($discount['discount_type'] == 'percentage') {
                                            echo htmlspecialchars($discount['discount_name']) . ' - ' . $discount['discount_value'] . '% off';
                                        } elseif ($discount['discount_type'] == 'buy_n_get_m') {
                                            echo htmlspecialchars($discount['discount_name']) . ' - Buy ' . $discount['buy_quantity'] . ' get ' . $discount['discount_value'] . ' free';
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1">
                            <br><br>
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>

        <br>
        <button><a href="view_orders.php">View Orders</a></button>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2024 Inventory Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
