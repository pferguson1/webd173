<?php
/**
 * Product API Endpoint
 * Provides JSON API for product operations
 */

require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        // Get all active products
        $category = $_GET['category'] ?? null;
        $featured = $_GET['featured'] ?? null;
        
        $sql = "SELECT * FROM products WHERE active = 1";
        $params = [];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($featured !== null) {
            $sql .= " AND featured = ?";
            $params[] = (int)$featured;
        }
        
        $sql .= " ORDER BY featured DESC, created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ]);
        break;
        
    case 'get':
        // Get single product by SKU or ID
        $sku = $_GET['sku'] ?? null;
        $id = $_GET['id'] ?? null;
        
        if ($sku) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE sku = ? AND active = 1");
            $stmt->execute([$sku]);
        } elseif ($id) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND active = 1");
            $stmt->execute([$id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'SKU or ID required']);
            exit;
        }
        
        $product = $stmt->fetch();
        
        if ($product) {
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
        }
        break;
        
    case 'categories':
        // Get all categories
        $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE active = 1 AND category IS NOT NULL ORDER BY category");
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        break;
        
    case 'search':
        // Search products
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'error' => 'Search query required']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT * FROM products 
            WHERE active = 1 
            AND (name LIKE ? OR description LIKE ? OR sku LIKE ?)
            ORDER BY featured DESC, name ASC
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $products = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ]);
        break;
        
    case 'check_stock':
        // Check stock availability
        $sku = $_POST['sku'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$sku) {
            echo json_encode(['success' => false, 'error' => 'SKU required']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE sku = ? AND active = 1");
        $stmt->execute([$sku]);
        $product = $stmt->fetch();
        
        if ($product) {
            $available = $product['stock'] >= $quantity;
            echo json_encode([
                'success' => true,
                'available' => $available,
                'stock' => $product['stock']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found']);
        }
        break;
        
    case 'reset_stock':
        // Reset stock to default values
        $sku = $_POST['sku'] ?? null;
        $default_stock = $_POST['default_stock'] ?? 100;
        
        try {
            if ($sku) {
                // Reset stock for specific product by SKU
                $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE sku = ?");
                $stmt->execute([$default_stock, $sku]);
                
                if ($stmt->rowCount() > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Stock reset for product $sku to $default_stock",
                        'affected_rows' => $stmt->rowCount()
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Product not found']);
                }
            } else {
                // Reset stock for all products to default values
                $stmt = $pdo->prepare("UPDATE products SET stock = ?");
                $stmt->execute([$default_stock]);
                
                echo json_encode([
                    'success' => true,
                    'message' => "All product stock reset to $default_stock",
                    'affected_rows' => $stmt->rowCount()
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'update_stock':
        // Update stock for specific product
        $sku = $_POST['sku'] ?? null;
        $stock = $_POST['stock'] ?? null;
        
        if (!$sku || $stock === null) {
            echo json_encode(['success' => false, 'error' => 'SKU and stock value required']);
            exit;
        }
        
        if (!is_numeric($stock) || $stock < 0) {
            echo json_encode(['success' => false, 'error' => 'Stock must be a non-negative number']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE sku = ?");
            $stmt->execute([$stock, $sku]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Stock updated for product $sku to $stock"
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action',
            'available_actions' => ['list', 'get', 'categories', 'search', 'check_stock', 'reset_stock', 'update_stock']
        ]);
}
?>
