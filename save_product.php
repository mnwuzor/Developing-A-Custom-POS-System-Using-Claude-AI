<?php
include 'db_connection.php';

// Check if action is provided
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Add or edit product
    if ($action === 'add' || $action === 'edit') {
        if (isset($_POST['productData'])) {
            $productData = json_decode($_POST['productData'], true);
            
            if ($productData) {
                $name = $conn->real_escape_string($productData['name']);
                $price = floatval($productData['price']);
                
                if ($action === 'add') {
                    // Add new product
                    $sql = "INSERT INTO products (product_name, price) VALUES ('$name', $price)";
                } else {
                    // Edit existing product
                    $id = intval($productData['id']);
                    $sql = "UPDATE products SET product_name='$name', price=$price WHERE product_id=$id";
                }
                
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(['status' => 'success', 'message' => 'Product saved successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error saving product: ' . $conn->error]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No data received']);
        }
    }
    // Delete product
    else if ($action === 'delete') {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
            
            // Check if product is used in sales
            $checkSql = "SELECT COUNT(*) as count FROM pos WHERE product IN (SELECT product_name FROM products WHERE product_id = $id)";
            $result = $conn->query($checkSql);
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Cannot delete product as it is used in sales']);
            } else {
                $sql = "DELETE FROM products WHERE product_id = $id";
                
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $conn->error]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}

$conn->close();
?>
