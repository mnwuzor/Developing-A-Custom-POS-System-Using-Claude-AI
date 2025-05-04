<?php
include 'db_connection.php';

// Check if form data is received
if (isset($_POST['salesData'])) {
    // Decode JSON data
    $salesData = json_decode($_POST['salesData'], true);
    
    if ($salesData) {
        // Extract header data
        $salesRefCode = $conn->real_escape_string($salesData['salesRefCode']);
        $userCode = $conn->real_escape_string($salesData['userCode']);
        $compCode = $conn->real_escape_string($salesData['compCode']);
        $date_sold = $conn->real_escape_string($salesData['date_sold']);
        $addedby = $conn->real_escape_string($salesData['addedby']);
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert each item
            foreach ($salesData['items'] as $item) {
                $product = $conn->real_escape_string($item['product']);
                $price = floatval($item['price']);
                $qty = intval($item['qty']);
                $total = floatval($item['total']);
                
                $sql = "INSERT INTO pos (product, salesRefCode, userCode, compCode, qty, price, total, date_sold, addedby) 
                        VALUES ('$product', '$salesRefCode', '$userCode', '$compCode', $qty, $price, $total, '$date_sold', '$addedby')";
                
                if (!$conn->query($sql)) {
                    throw new Exception("Error inserting sale: " . $conn->error);
                }
            }
            
            // Commit transaction
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Sale saved successfully']);
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
