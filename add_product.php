<?php
include('config.php');
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $tags = $_POST['tags'];
    $stock = $_POST['stock'];

    // Insert the product into the database
    $sql = "INSERT INTO Products (name, description, price, tags, stock) 
            VALUES ('$name', '$description', '$price', '$tags', '$stock')";
    if ($conn->query($sql) === TRUE) {
        echo "Product added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
    <h2>Add Product</h2>
    <form method="post" action="">
        Name: <input type="text" name="name" required><br>
        Description: <textarea name="description" required></textarea><br>
        Price: <input type="number" name="price" required><br>
        Tags: <input type="text" name="tags" required><br>
        Stock: <input type="number" name="stock" required><br>
        <button type="submit">Add Product</button>
    </form>
    <br>
    <a href="index.php">Admin Panel</a>
</body>
</html>
