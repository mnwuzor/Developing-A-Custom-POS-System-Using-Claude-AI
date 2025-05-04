<?php
include 'db_connection.php';

// Generate a random sales reference code
$salesRefCode = 'REF-' . date('Ymd') . '-' . rand(1000, 9999);

// Fetch products
$sql = "SELECT * FROM products ORDER BY product_name ASC";
$products = $conn->query($sql);

// Convert products to JSON for JavaScript
$productsJson = [];
if ($products->num_rows > 0) {
    while ($row = $products->fetch_assoc()) {
        $productsJson[$row['product_id']] = [
            'name' => $row['product_name'],
            'price' => $row['price']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Form</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Point of Sale Form</h1>
        
        <div class="action-buttons">
            <a href="index.php" class="btn back">Back to Sales List</a>
        </div>
        
        <form id="pos-form">
            <div class="form-header">
                <div class="form-group">
                    <label for="salesRefCode">Sales Reference Code:</label>
                    <input type="text" id="salesRefCode" name="salesRefCode" value="<?php echo $salesRefCode; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="userCode">User Code:</label>
                    <input type="text" id="userCode" name="userCode" required>
                </div>
                
                <div class="form-group">
                    <label for="compCode">Company Code:</label>
                    <input type="text" id="compCode" name="compCode" required>
                </div>
                
                <div class="form-group">
                    <label for="date_sold">Date Sold:</label>
                    <input type="date" id="date_sold" name="date_sold" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="addedby">Added By:</label>
                    <input type="text" id="addedby" name="addedby" required>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="product[]" class="product-select" required>
                                    <option value="">Select Product</option>
                                    <?php
                                    $products->data_seek(0);
                                    while ($row = $products->fetch_assoc()) {
                                        echo '<option value="' . $row['product_name'] . '" data-price="' . $row['price'] . '">' . $row['product_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="price[]" class="price-input" step="0.01" readonly>
                            </td>
                            <td>
                                <input type="number" name="qty[]" class="qty-input" value="1" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="total[]" class="total-input" step="0.01" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn remove-row">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                            <td><input type="number" id="grand-total" step="0.01" readonly></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="form-actions">
                <button type="button" id="add-row" class="btn add-row">Add New Row</button>
                <button type="submit" class="btn submit">Save Sale</button>
            </div>
        </form>
    </div>

    <script>
        // Store product data from PHP to JavaScript
        const products = <?php echo json_encode($productsJson); ?>;
        
        $(document).ready(function() {
            // Function to calculate row total
            function calculateRowTotal(row) {
                const price = parseFloat($(row).find('.price-input').val()) || 0;
                const qty = parseInt($(row).find('.qty-input').val()) || 0;
                const total = price * qty;
                $(row).find('.total-input').val(total.toFixed(2));
                calculateGrandTotal();
            }
            
            // Function to calculate grand total
            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.total-input').each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });
                $('#grand-total').val(grandTotal.toFixed(2));
            }
            
            // Initialize the first row
            calculateRowTotal($('#pos-table tbody tr:first'));
            
            // Handle product selection
            $(document).on('change', '.product-select', function() {
                const row = $(this).closest('tr');
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                
                row.find('.price-input').val(price);
                calculateRowTotal(row);
            });
            
            // Handle quantity change
            $(document).on('input', '.qty-input', function() {
                calculateRowTotal($(this).closest('tr'));
            });
            
            // Add new row
            $('#add-row').click(function() {
                const newRow = `
                    <tr>
                        <td>
                            <select name="product[]" class="product-select" required>
                                <option value="">Select Product</option>
                                <?php
                                $products->data_seek(0);
                                while ($row = $products->fetch_assoc()) {
                                    echo '<option value="' . $row['product_name'] . '" data-price="' . $row['price'] . '">' . $row['product_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="price[]" class="price-input" step="0.01" readonly>
                        </td>
                        <td>
                            <input type="number" name="qty[]" class="qty-input" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="total[]" class="total-input" step="0.01" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn remove-row">Remove</button>
                        </td>
                    </tr>
                `;
                
                $('#pos-table tbody').append(newRow);
            });
            
            // Remove row
            $(document).on('click', '.remove-row', function() {
                if ($('#pos-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                } else {
                    alert('You cannot remove the last row.');
                }
            });
            
            // Form submission
            $('#pos-form').submit(function(e) {
                e.preventDefault();
                
                const salesData = {
                    salesRefCode: $('#salesRefCode').val(),
                    userCode: $('#userCode').val(),
                    compCode: $('#compCode').val(),
                    date_sold: $('#date_sold').val(),
                    addedby: $('#addedby').val(),
                    items: []
                };
                
                // Validate form
                if (!salesData.userCode || !salesData.compCode || !salesData.date_sold || !salesData.addedby) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Collect items data
                let valid = true;
                $('#pos-table tbody tr').each(function() {
                    const product = $(this).find('.product-select').val();
                    const price = parseFloat($(this).find('.price-input').val());
                    const qty = parseInt($(this).find('.qty-input').val());
                    const total = parseFloat($(this).find('.total-input').val());
                    
                    if (!product || isNaN(price) || isNaN(qty) || qty < 1) {
                        valid = false;
                        return false;
                    }
                    
                    salesData.items.push({
                        product: product,
                        price: price,
                        qty: qty,
                        total: total
                    });
                });
                
                if (!valid) {
                    alert('Please check all products and quantities.');
                    return;
                }
                
                // Submit data via AJAX
                $.ajax({
                    url: 'save_sale.php',
                    type: 'POST',
                    data: {salesData: JSON.stringify(salesData)},
                    success: function(response) {
                        alert('Sale saved successfully!');
                        window.location.href = 'index.php';
                    },
                    error: function() {
                        alert('Error saving sale. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
