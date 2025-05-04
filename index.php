<?php
include 'db_connection.php';

// Dashboard data calculations
// 1. Total sales amount
$totalSalesQuery = "SELECT SUM(total) as total_sales FROM pos";
$totalSalesResult = $conn->query($totalSalesQuery);
$totalSales = 0;
if ($totalSalesResult->num_rows > 0) {
    $totalSales = $totalSalesResult->fetch_assoc()['total_sales'];
}

// 2. Total number of sales
$totalOrdersQuery = "SELECT COUNT(DISTINCT salesRefCode) as total_orders FROM pos";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = 0;
if ($totalOrdersResult->num_rows > 0) {
    $totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];
}

// 3. Top selling products
$topProductsQuery = "SELECT product, SUM(qty) as total_qty, SUM(total) as total_amount 
                    FROM pos 
                    GROUP BY product 
                    ORDER BY total_qty DESC 
                    LIMIT 5";
$topProductsResult = $conn->query($topProductsQuery);

// 4. Monthly sales for the current year
$currentYear = date('Y');
$monthlySalesQuery = "SELECT MONTH(date_sold) as month, SUM(total) as monthly_total 
                     FROM pos 
                     WHERE YEAR(date_sold) = '$currentYear' 
                     GROUP BY MONTH(date_sold) 
                     ORDER BY month";
$monthlySalesResult = $conn->query($monthlySalesQuery);

$monthlyData = array_fill(0, 12, 0); // Initialize array with zeros for all months
if ($monthlySalesResult->num_rows > 0) {
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthIndex = intval($row['month']) - 1; // Convert to 0-based index
        $monthlyData[$monthIndex] = floatval($row['monthly_total']);
    }
}

// 5. Recent sales
$recentSalesQuery = "SELECT * FROM pos ORDER BY created_at DESC LIMIT 5";
$recentSalesResult = $conn->query($recentSalesQuery);

// Fetch all sales data for the main table
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Product Sales System</h1>
        
        <div class="action-buttons">
            <a href="pos_form.php" class="btn add-new">Add New Sale</a>
            <a href="products_management.php" class="btn">Manage Products</a>
        </div>
        
        <!-- Dashboard Section -->
        <div class="dashboard">
            <h2>Sales Dashboard</h2>
            
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-title">Total Sales</div>
                    <div class="card-value">$<?php echo number_format($totalSales, 2); ?></div>
                </div>
                
                <div class="card">
                    <div class="card-title">Total Orders</div>
                    <div class="card-value"><?php echo $totalOrders; ?></div>
                </div>
                
                <div class="card">
                    <div class="card-title">Avg. Order Value</div>
                    <div class="card-value">$<?php echo $totalOrders > 0 ? number_format($totalSales / $totalOrders, 2) : '0.00'; ?></div>
                </div>
            </div>
            
            <div class="dashboard-charts" style="display:none">
                <div class="chart-container">
                    <h3>Monthly Sales (<?php echo $currentYear; ?>)</h3>
                    <canvas id="monthlySalesChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3>Top Selling Products</h3>
                    <div class="table-responsive">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($topProductsResult->num_rows > 0): ?>
                                    <?php while ($row = $topProductsResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['product']; ?></td>
                                            <td><?php echo $row['total_qty']; ?></td>
                                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="recent-sales">
                <h3>Recent Sales</h3>
                <div class="table-responsive">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Ref Code</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentSalesResult->num_rows > 0): ?>
                                <?php while ($row = $recentSalesResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['salesRefCode']; ?></td>
                                        <td><?php echo $row['product']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td>$<?php echo number_format($row['total'], 2); ?></td>
                                        <td><?php echo $row['date_sold']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No recent sales</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="sales-container">
            <h2>Sales List</h2>
            
            <?php if ($result->num_rows > 0) { ?>
                <div class="table-responsive" style="font-size:14px">
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
            // Monthly sales chart
            var ctx = document.getElementById('monthlySalesChart').getContext('2d');
            var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var monthlyData = <?php echo json_encode($monthlyData); ?>;
            
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Monthly Sales ($)',
                        data: monthlyData,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '
</body>
</html>
 + value;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
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