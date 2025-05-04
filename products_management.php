<?php
include 'db_connection.php';

// Fetch all products
$sql = "SELECT * FROM products ORDER BY product_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Products Management</h1>
        
        <div class="action-buttons">
            <a href="index.php" class="btn back">Back to Sales List</a>
            <button id="add-product-btn" class="btn add-new">Add New Product</button>
        </div>
        
        <div class="table-responsive">
            <table id="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['product_id']; ?></td>
                                <td><?php echo $row['product_name']; ?></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td class="actions">
                                    <button class="btn edit edit-product" data-id="<?php echo $row['product_id']; ?>" 
                                            data-name="<?php echo $row['product_name']; ?>" 
                                            data-price="<?php echo $row['price']; ?>">Edit</button>
                                    <button class="btn delete delete-product" data-id="<?php echo $row['product_id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Product Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modal-title">Add New Product</h2>
            <form id="product-form">
                <input type="hidden" id="product-id">
                
                <div class="form-group">
                    <label for="product-name">Product Name:</label>
                    <input type="text" id="product-name" required>
                </div>
                
                <div class="form-group">
                    <label for="product-price">Price:</label>
                    <input type="number" id="product-price" step="0.01" min="0" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn submit">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Open modal for adding product
            $('#add-product-btn').click(function() {
                $('#modal-title').text('Add New Product');
                $('#product-id').val('');
                $('#product-name').val('');
                $('#product-price').val('');
                $('#product-modal').css('display', 'block');
            });
            
            // Open modal for editing product
            $('.edit-product').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = $(this).data('price');
                
                $('#modal-title').text('Edit Product');
                $('#product-id').val(id);
                $('#product-name').val(name);
                $('#product-price').val(price);
                $('#product-modal').css('display', 'block');
            });
            
            // Close modal
            $('.close').click(function() {
                $('#product-modal').css('display', 'none');
            });
            
            // Close modal when clicking outside
            $(window).click(function(e) {
                if ($(e.target).is('#product-modal')) {
                    $('#product-modal').css('display', 'none');
                }
            });
            
            // Handle product form submission
            $('#product-form').submit(function(e) {
                e.preventDefault();
                
                const productData = {
                    id: $('#product-id').val(),
                    name: $('#product-name').val(),
                    price: parseFloat($('#product-price').val())
                };
                
                // Action depends on whether we're adding or editing
                const isEdit = productData.id !== '';
                
                $.ajax({
                    url: 'save_product.php',
                    type: 'POST',
                    data: {
                        action: isEdit ? 'edit' : 'add',
                        productData: JSON.stringify(productData)
                    },
                    success: function(response) {
                        alert(isEdit ? 'Product updated successfully!' : 'Product added successfully!');
                        location.reload();
                    },
                    error: function() {
                        alert('Error saving product. Please try again.');
                    }
                });
            });
            
            // Handle product deletion
            $('.delete-product').click(function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    const id = $(this).data('id');
                    
                    $.ajax({
                        url: 'save_product.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        success: function(response) {
                            alert('Product deleted successfully!');
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting product. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
