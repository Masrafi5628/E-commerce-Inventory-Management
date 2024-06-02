<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include('config.php');
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// Handle form submissions to add/edit discounts
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $discount_name = $_POST['discount_name'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $buy_quantity = isset($_POST['buy_quantity']) && $_POST['buy_quantity'] !== '' ? $_POST['buy_quantity'] : NULL;
    $tags = $_POST['tags'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (isset($_POST['discount_id']) && !empty($_POST['discount_id'])) {
        // Edit discount
        $discount_id = $_POST['discount_id'];
        $stmt = $conn->prepare("UPDATE Discounts SET discount_name = ?, discount_type = ?, discount_value = ?, buy_quantity = ?, tags = ?, start_date = ?, end_date = ? WHERE discount_id = ?");
        $stmt->bind_param("ssdisssi", $discount_name, $discount_type, $discount_value, $buy_quantity, $tags, $start_date, $end_date, $discount_id);
    } else {
        // Add new discount
        $stmt = $conn->prepare("INSERT INTO Discounts (discount_name, discount_type, discount_value, buy_quantity, tags, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdisss", $discount_name, $discount_type, $discount_value, $buy_quantity, $tags, $start_date, $end_date);
    }

    if ($stmt->execute()) {
        echo "Discount saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle delete requests
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Discounts WHERE discount_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "Discount deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch existing discounts from the database
$sql = "SELECT * FROM Discounts";
$result = $conn->query($sql);

$discounts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $discounts[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Discounts</title>
    <script>
        function toggleBuyQuantityField() {
            var discountType = document.getElementById("discount_type").value;
            var buyQuantityField = document.getElementById("buy_quantity_field");
            if (discountType === "buy_n_get_m") {
                buyQuantityField.style.display = "block";
            } else {
                buyQuantityField.style.display = "none";
            }
        }

        function editDiscount(discount) {
            document.getElementById("discount_id").value = discount.discount_id;
            document.getElementById("discount_name").value = discount.discount_name;
            document.getElementById("discount_type").value = discount.discount_type;
            document.getElementById("discount_value").value = discount.discount_value;
            document.getElementById("buy_quantity").value = discount.buy_quantity;
            document.getElementById("tags").value = discount.tags;
            document.getElementById("start_date").value = discount.start_date;
            document.getElementById("end_date").value = discount.end_date;

            toggleBuyQuantityField();
        }
    </script>
</head>
<body>
    <h2>Manage Discounts</h2>
    
    <!-- Form to add/edit a discount -->
    <form method="post" action="">
        <input type="hidden" id="discount_id" name="discount_id">
        
        <label for="discount_name">Discount Name:</label>
        <input type="text" id="discount_name" name="discount_name" required><br>
        
        <label for="discount_type">Discount Type:</label>
        <select id="discount_type" name="discount_type" onchange="toggleBuyQuantityField()">
            <option value="percentage">Percentage</option>
            <option value="buy_n_get_m">Buy N Get M</option>
        </select><br>
        
        <label for="discount_value">Discount Value:</label>
        <input type="number" id="discount_value" name="discount_value" step="0.01" required><br>
        
        <div id="buy_quantity_field" style="display: none;">
            <label for="buy_quantity">Buy Quantity (for "Buy N Get M"):</label>
            <input type="number" id="buy_quantity" name="buy_quantity"><br>
        </div>

        <label for="tags">Tags (comma-separated):</label>
        <input type="text" id="tags" name="tags" required><br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required><br>
        
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required><br>
        
        <button type="submit">Save Discount</button>
    </form>
    <a href="index.php">Admin Panel</a>

    <!-- Display existing discounts -->
    <h3>Existing Discounts</h3>
    <table border="1">
        <tr>
            <th>Discount Name</th>
            <th>Discount Type</th>
            <th>Discount Value</th>
            <th>Buy Quantity</th>
            <th>Tags</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($discounts as $discount): ?>
            <tr>
                <td><?php echo htmlspecialchars($discount['discount_name']); ?></td>
                <td><?php echo htmlspecialchars($discount['discount_type']); ?></td>
                <td><?php echo htmlspecialchars($discount['discount_value']); ?></td>
                <td><?php echo htmlspecialchars($discount['buy_quantity']); ?></td>
                <td><?php echo htmlspecialchars($discount['tags']); ?></td>
                <td><?php echo htmlspecialchars($discount['start_date']); ?></td>
                <td><?php echo htmlspecialchars($discount['end_date']); ?></td>
                <td>
                    <button onclick='editDiscount(<?php echo json_encode($discount); ?>)'>Edit</button>
                    <a href="?delete_id=<?php echo $discount['discount_id']; ?>" onclick="return confirm('Are you sure you want to delete this discount?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>


</body>
</html>
