<?php
include 'db_connection.php';

// Check if ID is provided
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Delete the sale record
    $sql = "DELETE FROM pos WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Sale deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error deleting sale: ' . $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
}

$conn->close();
?>
