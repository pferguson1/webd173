<?php
/**
 * Stock Manager - Admin Utility
 * Provides interface for managing product stock levels
 */

require_once '../config/config.php';

// Check if user is admin (simple check - enhance with proper authentication in production)
session_start();
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// For demo purposes, allow access (in production, implement proper admin authentication)
$is_admin = true;

if (!$is_admin) {
    die('Access denied. Admin privileges required.');
}

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'reset_all':
            $default_stock = $_POST['default_stock'] ?? 100;
            
            try {
                $stmt = $pdo->prepare("UPDATE products SET stock = ?");
                $stmt->execute([$default_stock]);
                $message = "Successfully reset stock for {$stmt->rowCount()} products to {$default_stock}";
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
            break;
            
        case 'reset_single':
            $sku = $_POST['sku'] ?? '';
            $stock = $_POST['stock'] ?? 100;
            
            if (empty($sku)) {
                $error = "SKU is required";
                break;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE sku = ?");
                $stmt->execute([$stock, $sku]);
                
                if ($stmt->rowCount() > 0) {
                    $message = "Successfully updated stock for product {$sku} to {$stock}";
                } else {
                    $error = "Product with SKU {$sku} not found";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
            break;
    }
}

// Get current products
try {
    $stmt = $pdo->query("SELECT sku, name, stock, price FROM products ORDER BY sku");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Failed to load products: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager - ArtShop Admin</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .container { margin-top: 20px; }
        .alert { margin-bottom: 20px; }
        .card { margin-bottom: 20px; }
        .product-row { border-bottom: 1px solid #eee; padding: 10px 0; }
        .low-stock { background-color: #fff3cd; }
        .out-of-stock { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stock Manager</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Reset All Stock -->
        <div class="card">
            <div class="card-header">
                <h3>Reset All Stock</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="reset_all">
                    <div class="mb-3">
                        <label for="default_stock" class="form-label">Default Stock Level</label>
                        <input type="number" class="form-control" id="default_stock" name="default_stock" value="100" min="0">
                    </div>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to reset stock for ALL products?')">Reset All Stock</button>
                </form>
            </div>
        </div>
        
        <!-- Update Individual Product -->
        <div class="card">
            <div class="card-header">
                <h3>Update Individual Product</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="reset_single">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sku" class="form-label">Product SKU</label>
                            <select class="form-control" id="sku" name="sku" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo htmlspecialchars($product['sku']); ?>">
                                        <?php echo htmlspecialchars($product['sku'] . ' - ' . $product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">New Stock Level</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="100" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block">Update Stock</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Current Stock Levels -->
        <div class="card">
            <div class="card-header">
                <h3>Current Stock Levels</h3>
            </div>
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): 
                                    $stockClass = '';
                                    $status = 'In Stock';
                                    
                                    if ($product['stock'] == 0) {
                                        $stockClass = 'out-of-stock';
                                        $status = 'Out of Stock';
                                    } elseif ($product['stock'] <= 15) {
                                        $stockClass = 'low-stock';
                                        $status = 'Low Stock';
                                    }
                                ?>
                                    <tr class="<?php echo $stockClass; ?>">
                                        <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td><?php echo $status; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- API Usage -->
        <div class="card">
            <div class="card-header">
                <h3>API Usage</h3>
            </div>
            <div class="card-body">
                <h5>Reset All Stock (POST request)</h5>
                <code>
                    POST /api/products.php<br>
                    action=reset_stock<br>
                    default_stock=100
                </code>
                
                <h5 class="mt-3">Reset Single Product Stock (POST request)</h5>
                <code>
                    POST /api/products.php<br>
                    action=reset_stock<br>
                    sku=WARR001<br>
                    default_stock=50
                </code>
                
                <h5 class="mt-3">Update Single Product Stock (POST request)</h5>
                <code>
                    POST /api/products.php<br>
                    action=update_stock<br>
                    sku=WARR001<br>
                    stock=25
                </code>
            </div>
        </div>
        
        <div class="mb-4">
            <a href="index.php" class="btn btn-secondary">Back to Admin Panel</a>
        </div>
    </div>
</body>
</html>