<?php
include 'db_connection.php';

// Fetch all sales data
$sql = "SELECT * FROM pos ORDER BY date_sold DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Sales System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Product Sales System</h1>
        
        <div class="action-buttons">
            <a href="pos_form.php" class="btn add-new">Add New Sale</a>
        </div>
        
        <div class="sales-container">
            <h2>Sales List</h2>
            
            <?php if ($result->num_rows > 0) { ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Ref Code</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Date Sold</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand_total = 0;
                            while ($row = $result->fetch_assoc()) {
                                $grand_total += $row['total'];
                            ?>
                            <tr>
                                <td><?php echo $row['salesRefCode']; ?></td>
                                <td><?php echo $row['product']; ?></td>
                                <td><?php echo $row['qty']; ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td>$<?php echo number_format($row['total'], 2); ?></td>
                                <td><?php echo $row['date_sold']; ?></td>
                                <td><?php echo $row['addedby']; ?></td>
                                <td class="actions">
                                    <a href="edit_sale.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
                                    <a href="javascript:void(0);" class="btn delete" data-id="<?php echo $row['id']; ?>">Delete</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                <td><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php } else { ?>
                <p>No sales records found.</p>
            <?php } ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle delete button
            $('.delete').click(function() {
                if (confirm('Are you sure you want to delete this sale?')) {
                    var saleId = $(this).data('id');
                    
                    $.ajax({
                        url: 'delete_sale.php',
                        type: 'POST',
                        data: {id: saleId},
                        success: function(response) {
                            alert('Sale deleted successfully!');
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting sale. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
