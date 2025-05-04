<?php
include 'db_connection.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch sale record
$sql = "SELECT * FROM pos WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$sale = $result->fetch_assoc();

// Fetch products
$sql = "SELECT * FROM products ORDER BY product_name ASC";
$products = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sale</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Edit Sale</h1>
        
        <div class="action-buttons">
            <a href="index.php" class="btn back">Back to Sales List</a>
        </div>
        
        <form id="edit-form">
            <input type="hidden" id="sale-id" value="<?php echo $id; ?>">
            
            <div class="form-header">
                <div class="form-group">
                    <label for="salesRefCode">Sales Reference Code:</label>
                    <input type="text" id="salesRefCode" name="salesRefCode" value="<?php echo $sale['salesRefCode']; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="userCode">User Code:</label>
                    <input type="text" id="userCode" name="userCode" value="<?php echo $sale['userCode']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="compCode">Company Code:</label>
                    <input type="text" id="compCode" name="compCode" value="<?php echo $sale['compCode']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_sold">Date Sold:</label>
                    <input type="date" id="date_sold" name="date_sold" value="<?php echo $sale['date_sold']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="addedby">Added By:</label>
                    <input type="text" id="addedby" name="addedby" value="<?php echo $sale['addedby']; ?>" required>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="pos-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="product" class="product-select" required>
                                    <option value="">Select Product</option>
                                    <?php
                                    while ($row = $products->fetch_assoc()) {
                                        $selected = ($row['product_name'] == $sale['product']) ? 'selected' : '';
                                        echo '<option value="' . $row['product_name'] . '" data-price="' . $row['price'] . '" ' . $selected . '>' . $row['product_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="price" class="price-input" step="0.01" value="<?php echo $sale['price']; ?>" readonly>
                            </td>
                            <td>
                                <input type="number" name="qty" class="qty-input" value="<?php echo $sale['qty']; ?>" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="total" class="total-input" step="0.01" value="<?php echo $sale['total']; ?>" readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn submit">Update Sale</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Function to calculate total
            function calculateTotal() {
                const price = parseFloat($('.price-input').val()) || 0;
                const qty = parseInt($('.qty-input').val()) || 0;
                const total = price * qty;
                $('.total-input').val(total.toFixed(2));
            }
            
            // Handle product selection
            $('.product-select').change(function() {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                
                $('.price-input').val(price);
                calculateTotal();
            });
            
            // Handle quantity change
            $('.qty-input').on('input', function() {
                calculateTotal();
            });
            
            // Form submission
            $('#edit-form').submit(function(e) {
                e.preventDefault();
                
                const saleId = $('#sale-id').val();
                const saleData = {
                    id: saleId,
                    salesRefCode: $('#salesRefCode').val(),
                    userCode: $('#userCode').val(),
                    compCode: $('#compCode').val(),
                    product: $('.product-select').val(),
                    price: parseFloat($('.price-input').val()),
                    qty: parseInt($('.qty-input').val()),
                    total: parseFloat($('.total-input').val()),
                    date_sold: $('#date_sold').val(),
                    addedby: $('#addedby').val()
                };
                
                // Validate form
                if (!saleData.userCode || !saleData.compCode || !saleData.product || !saleData.date_sold || !saleData.addedby || isNaN(saleData.qty) || saleData.qty < 1) {
                    alert('Please fill in all required fields with valid values.');
                    return;
                }
                
                // Submit data via AJAX
                $.ajax({
                    url: 'update_sale.php',
                    type: 'POST',
                    data: {saleData: JSON.stringify(saleData)},
                    success: function(response) {
                        alert('Sale updated successfully!');
                        window.location.href = 'index.php';
                    },
                    error: function() {
                        alert('Error updating sale. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
