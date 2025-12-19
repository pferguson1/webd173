<?php
/**
 * Admin - Product Management
 */
require_once '../config.php';
requireAdmin();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'update') {
        $id = $_POST['id'] ?? null;
        $sku = sanitizeInput($_POST['sku']);
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $category = sanitizeInput($_POST['category']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $active = isset($_POST['active']) ? 1 : 0;
        $image = sanitizeInput($_POST['image']);
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO products (sku, name, description, price, stock, category, featured, active, image)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$sku, $name, $description, $price, $stock, $category, $featured, $active, $image]);
                setFlashMessage('Product added successfully!', 'success');
            } else {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET sku=?, name=?, description=?, price=?, stock=?, category=?, featured=?, active=?, image=?
                    WHERE id=?
                ");
                $stmt->execute([$sku, $name, $description, $price, $stock, $category, $featured, $active, $image, $id]);
                setFlashMessage('Product updated successfully!', 'success');
            }
            redirect('products.php');
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        setFlashMessage('Product deleted successfully!', 'success');
        redirect('products.php');
    }
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch();
}

// Get all products
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="row m-0">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content-area">
            <h2 class="mb-4">Product Management</h2>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($flash['message']); ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#productModal" onclick="clearForm()">
                <i class="fa fa-plus"></i> Add New Product
            </button>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><img src="../<?php echo htmlspecialchars($product['image']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo formatCurrency($product['price']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $product['stock'] <= 5 ? 'danger' : 'success'; ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $product['active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $product['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $editProduct ? 'Edit' : 'Add'; ?> Product</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editProduct ? 'update' : 'add'; ?>">
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SKU *</label>
                                    <input type="text" name="sku" class="form-control" required
                                           value="<?php echo $editProduct['sku'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" required
                                           value="<?php echo $editProduct['name'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $editProduct['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Price *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required
                                           value="<?php echo $editProduct['price'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stock *</label>
                                    <input type="number" name="stock" class="form-control" required
                                           value="<?php echo $editProduct['stock'] ?? '0'; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category</label>
                                    <input type="text" name="category" class="form-control"
                                           value="<?php echo $editProduct['category'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="text" name="image" class="form-control"
                                   value="<?php echo $editProduct['image'] ?? ''; ?>">
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="featured" class="form-check-input" id="featured"
                                   <?php echo ($editProduct['featured'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="active" class="form-check-input" id="active"
                                   <?php echo ($editProduct['active'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($editProduct): ?>
    <script>
        $(document).ready(function() {
            $('#productModal').modal('show');
        });
    </script>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
