<?php
include 'db_connection.php';

// Check if form data is received
if (isset($_POST['saleData'])) {
    // Decode JSON data
    $saleData = json_decode($_POST['saleData'], true);
    
    if ($saleData) {
        // Extract data
        $id = intval($saleData['id']);
        $salesRefCode = $conn->real_escape_string($saleData['salesRefCode']);
        $userCode = $conn->real_escape_string($saleData['userCode']);
        $compCode = $conn->real_escape_string($saleData['compCode']);
        $product = $conn->real_escape_string($saleData['product']);
        $price = floatval($saleData['price']);
        $qty = intval($saleData['qty']);
        $total = floatval($saleData['total']);
        $date_sold = $conn->real_escape_string($saleData['date_sold']);
        $addedby = $conn->real_escape_string($saleData['addedby']);
        
        $sql = "UPDATE pos SET 
                salesRefCode='$salesRefCode', 
                userCode='$userCode', 
                compCode='$compCode', 
                product='$product', 
                price=$price, 
                qty=$qty, 
                total=$total, 
                date_sold='$date_sold', 
                addedby='$addedby' 
                WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Sale updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error updating sale: ' . $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}

$conn->close();
?>
