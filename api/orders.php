<?php
/**
 * Order Processing System
 * Handles order creation and management
 */

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POST method required']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        // Create new order
        try {
            $cart = json_decode($_POST['cart'] ?? '[]', true);
            $customerEmail = sanitizeInput($_POST['email'] ?? '');
            $customerName = sanitizeInput($_POST['name'] ?? '');
            $customerPhone = sanitizeInput($_POST['phone'] ?? '');
            $paymentMethod = sanitizeInput($_POST['payment_method'] ?? 'credit_card');
            
            if (empty($cart) || empty($customerEmail) || empty($customerName)) {
                throw new Exception('Missing required fields');
            }
            
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            
            // Calculate totals
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($cart as $sku => $item) {
                // Verify product exists and has stock
                $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE sku = ?");
                $stmt->execute([$sku]);
                $product = $stmt->fetch();
                
                if (!$product) {
                    throw new Exception("Product $sku not found");
                }
                
                if ($product['stock'] < $item['qty']) {
                    throw new Exception("Insufficient stock for {$product['name']}");
                }
                
                $itemSubtotal = $product['price'] * $item['qty'];
                $subtotal += $itemSubtotal;
                
                $orderItems[] = [
                    'product_id' => $product['id'],
                    'sku' => $sku,
                    'name' => $product['name'],
                    'quantity' => $item['qty'],
                    'price' => $product['price'],
                    'subtotal' => $itemSubtotal
                ];
            }
            
            $tax = 0;
            $shipping = 0;
            $total = $subtotal + $tax + $shipping;
            
            // Start transaction
            $pdo->beginTransaction();
            
            // Insert order
            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    order_number, customer_email, customer_name, customer_phone,
                    payment_method, subtotal, tax, shipping, total, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $orderNumber, $customerEmail, $customerName, $customerPhone,
                $paymentMethod, $subtotal, $tax, $shipping, $total
            ]);
            
            $orderId = $pdo->lastInsertId();
            
            // Insert order items and update stock
            $stmt = $pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, sku, product_name, quantity, price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $updateStockStmt = $pdo->prepare("
                UPDATE products SET stock = stock - ? WHERE id = ?
            ");
            
            foreach ($orderItems as $item) {
                $stmt->execute([
                    $orderId, $item['product_id'], $item['sku'], $item['name'],
                    $item['quantity'], $item['price'], $item['subtotal']
                ]);
                
                $updateStockStmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => $total
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'get':
        // Get order by ID or order number
        $orderId = $_GET['id'] ?? null;
        $orderNumber = $_GET['order_number'] ?? null;
        
        try {
            if ($orderId) {
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
                $stmt->execute([$orderId]);
            } elseif ($orderNumber) {
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
                $stmt->execute([$orderNumber]);
            } else {
                throw new Exception('Order ID or order number required');
            }
            
            $order = $stmt->fetch();
            
            if (!$order) {
                throw new Exception('Order not found');
            }
            
            // Get order items
            $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->execute([$order['id']]);
            $order['items'] = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $order]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'update_status':
        // Update order status (admin only)
        requireAdmin();
        
        try {
            $orderId = $_POST['order_id'] ?? null;
            $status = sanitizeInput($_POST['status'] ?? '');
            
            if (!$orderId || !$status) {
                throw new Exception('Order ID and status required');
            }
            
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            
            echo json_encode(['success' => true, 'message' => 'Order status updated']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action',
            'available_actions' => ['create', 'get', 'update_status']
        ]);
}
?>
