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

// Fetch products from the database
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <style>
        .product {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .admin-section {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $is_admin ? 'Admin' : 'User'; ?></h2>
    <p><a href="logout.php">Logout</a></p>

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
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <p>Tags: <?php echo htmlspecialchars($product['tags']); ?></p>
                    <p>Discount: <?php echo $product['discount_type'] == 'percentage' ? $product['discount_value'] . '%' : 'Buy ' . $product['discount_buy'] . ' get ' . $product['discount_get']; ?></p>
                    <form method="post" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
