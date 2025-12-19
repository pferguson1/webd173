<?php
/**
 * Admin Dashboard - Main Panel
 */
require_once '../config.php';
requireAdmin();

// Get statistics
$statsQuery = [
    'total_products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'active_products' => $pdo->query("SELECT COUNT(*) FROM products WHERE active = 1")->fetchColumn(),
    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0,
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];

// Get recent orders
$recentOrders = $pdo->query("
    SELECT * FROM orders 
    ORDER BY created_at DESC 
    LIMIT 10
")->fetchAll();

// Get low stock products
$lowStockProducts = $pdo->query("
    SELECT * FROM products 
    WHERE stock <= 5 AND active = 1 
    ORDER BY stock ASC 
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: #333;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #007bff;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 32px;
            color: #333;
            margin: 0;
        }
        .stat-card p {
            color: #666;
            margin: 0;
        }
        .content-area {
            padding: 30px;
        }
        table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="row m-0">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h3 class="text-center mb-4"><?php echo SITE_NAME; ?></h3>
            <nav>
                <a href="index.php" class="active"><i class="fa fa-dashboard"></i> Dashboard</a>
                <a href="products.php"><i class="fa fa-shopping-bag"></i> Products</a>
                <a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fa fa-users"></i> Users</a>
                <hr style="border-color: #555;">
                <a href="../products.php"><i class="fa fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 content-area">
            <h2 class="mb-4">Dashboard Overview</h2>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <p>Total Products</p>
                        <h3><?php echo $statsQuery['total_products']; ?></h3>
                        <small class="text-success"><?php echo $statsQuery['active_products']; ?> active</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p>Total Orders</p>
                        <h3><?php echo $statsQuery['total_orders']; ?></h3>
                        <small class="text-warning"><?php echo $statsQuery['pending_orders']; ?> pending</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p>Revenue</p>
                        <h3><?php echo formatCurrency($statsQuery['total_revenue']); ?></h3>
                        <small class="text-muted">Completed orders</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p>Registered Users</p>
                        <h3><?php echo $statsQuery['total_users']; ?></h3>
                        <small class="text-muted">Total accounts</small>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo formatCurrency($order['total']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $order['status'] === 'completed' ? 'success' : 
                                            ($order['status'] === 'pending' ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <?php if (count($lowStockProducts) > 0): ?>
            <div class="card mt-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fa fa-exclamation-triangle"></i> Low Stock Alert</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><span class="badge badge-danger"><?php echo $product['stock']; ?></span></td>
                                <td><?php echo formatCurrency($product['price']); ?></td>
                                <td>
                                    <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
