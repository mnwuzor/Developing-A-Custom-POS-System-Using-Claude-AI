<?php
include 'db_connection.php';

// Fetch products
$sql = "SELECT * FROM products ORDER BY product_name ASC";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['product_id'],
            'name' => $row['product_name'],
            'price' => $row['price']
        ];
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($products);

$conn->close();
?>
