<?php
// Start session to access cart data
session_start();

// Load configuration
require_once 'config.php';
require_once 'mailer.php';

// Database connection — use credentials from config.php (DB_HOST/DB_NAME/DB_USER/DB_PASS)
$dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Initialize content for order summary
$content = '';
$cartTotal = 0;
$orderItems = array();

// Build order summary from session cart
if (!empty($_SESSION['products'])) {
    $content .= "<strong>Order Details:</strong><br>";
    foreach ($_SESSION['products'] as $sku => $product) {
        $itemTotal = $product['price'] * $product['qty'];
        $cartTotal += $itemTotal;
        $itemStr = $product['name'] . " (SKU: $sku) - Qty: " . $product['qty'] . " x $" . $product['price'] . " = $" . $itemTotal;
        $content .= $itemStr . "<br>";
        $orderItems[] = $itemStr;
    }
    $content .= "<br><strong>Cart Total: $" . number_format($cartTotal, 2) . "</strong><br>";
} else {
    $content .= "No items in cart.";
}

// Collect customer info from POST
$customerEmail = isset($_POST['email']) ? $_POST['email'] : '';
$customerName = isset($_POST['name']) ? $_POST['name'] : '';
$customerPhone = isset($_POST['phone']) ? $_POST['phone'] : '';
$shippingAddress = isset($_POST['address']) ? $_POST['address'] : '';

// Add POST data to content
if (!empty($_POST)) {
    $content .= "<br><strong>Customer Information:</strong><br>";
    foreach ($_POST as $key => $value) {
        if ($key !== 'sku') { // Skip the hidden sku field
            $content .= ucfirst(str_replace('_', ' ', $key)) . ": " . htmlspecialchars($value) . "<br>";
        }
    }
}

// Save order to database
try {
    $orderItemsJson = json_encode($orderItems);

    $query = "INSERT INTO orders (customer_email, customer_name, customer_phone, shipping_address, order_total, order_items, payment_status) 
              VALUES (:email, :name, :phone, :address, :total, :items, 'completed')";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $customerEmail);
    $stmt->bindParam(':name', $customerName);
    $stmt->bindParam(':phone', $customerPhone);
    $stmt->bindParam(':address', $shippingAddress);
    $stmt->bindParam(':total', $cartTotal);
    $stmt->bindParam(':items', $orderItemsJson);
    $stmt->execute();

    $orderId = $dbh->lastInsertId();
    $content = "Order #" . $orderId . " received!<br><br>" . $content;
    if (DEBUG) @file_put_contents(__DIR__ . '/order-insert.log', '[' . date('Y-m-d H:i:s') . "] SUCCESS order_id=" . $orderId . " email=" . $customerEmail . " total=" . $cartTotal . " items=" . $orderItemsJson . "\n", FILE_APPEND);

    // Insert each line item into order_items table for detailed tracking
    try {
        $itemStmt = $dbh->prepare("INSERT INTO order_items (order_id, sku, product_name, qty, unit_price, line_total) VALUES (:order_id, :sku, :name, :qty, :unit_price, :line_total)");
        foreach ($_SESSION['products'] as $sku => $product) {
            $qty = (int)$product['qty'];
            $unit = (float)$product['price'];
            $line = $qty * $unit;
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':sku' => $sku,
                ':name' => $product['name'],
                ':qty' => $qty,
                ':unit_price' => $unit,
                ':line_total' => $line
            ]);
            if (DEBUG) @file_put_contents(__DIR__ . '/order-insert.log', '[' . date('Y-m-d H:i:s') . "] ITEM order_id=" . $orderId . " sku=" . $sku . " qty=" . $qty . " unit=" . $unit . " line=" . $line . "\n", FILE_APPEND);
            // Deduct stock from products table
            try {
                $stockStmt = $dbh->prepare('UPDATE products SET stock = stock - :qty WHERE sku = :sku AND stock >= :qty');
                $stockStmt->execute([':qty' => $qty, ':sku' => $sku]);
            } catch (Exception $se) {
                if (DEBUG) @file_put_contents(__DIR__ . '/order-insert.log', '[' . date('Y-m-d H:i:s') . "] STOCK_ERROR order_id=" . $orderId . " sku=" . $sku . " msg=" . $se->getMessage() . "\n", FILE_APPEND);
            }
        }
    } catch (Exception $ie) {
        if (DEBUG) @file_put_contents(__DIR__ . '/order-insert.log', '[' . date('Y-m-d H:i:s') . "] ITEM_ERROR order_id=" . $orderId . " msg=" . $ie->getMessage() . "\n", FILE_APPEND);
    }
} catch (Exception $e) {
    $content = "Order processed (database save failed): " . $e->getMessage() . "<br><br>" . $content;
    if (DEBUG) @file_put_contents(__DIR__ . '/order-insert.log', '[' . date('Y-m-d H:i:s') . "] ERROR " . $e->getMessage() . " email=" . $customerEmail . " total=" . $cartTotal . " items=" . $orderItemsJson . "\n", FILE_APPEND);
}

// Send confirmation emails using PHP mail() function
$emailStatus = "";
// Legacy headers removed (handled inside mailer helper)

// Build order content for email
$orderContent = $content;

// Email to site owner
$ownerSubject = "New Order #" . (isset($orderId) ? $orderId : "unknown") . " - " . SITE_NAME;
$ownerMessage = "<h2>New Order Received!</h2>" . $orderContent;

if (send_app_mail(SITE_EMAIL, $ownerSubject, $ownerMessage)) {
    $emailStatus .= "✓ Order notification sent to site owner. ";
} else {
    $emailStatus .= "⚠ Could not send order notification. ";
}

// Email to customer (if email provided)
if (!empty($customerEmail)) {
    $customerSubject = "Order Confirmation #" . (isset($orderId) ? $orderId : "unknown") . " - " . SITE_NAME;
    $customerMessage = "<h2>Thank You for Your Order!</h2><p>Hi " . htmlspecialchars($customerName) . ",</p>" . $orderContent . "<br><br><p>Your order has been saved and we will process it shortly.</p>";

    if (send_app_mail($customerEmail, $customerSubject, $customerMessage)) {
        $emailStatus .= "✓ Confirmation email sent to customer.";
    } else {
        $emailStatus .= "⚠ Could not send confirmation email.";
    }
} else {
    $emailStatus .= "(No customer email provided)";
}

// Clear the cart after processing
$_SESSION['products'] = array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin-top:50px; width:600px;">
        <div class="alert alert-success">
            <h1>Thank you!</h1>
            <p>We received your order. A confirmation has been sent to the site owner.</p>
            <?php if (!empty($emailStatus)): ?>
                <p style="margin-top:15px; font-size:12px; color:#666;"><em><?php echo $emailStatus; ?></em></p>
            <?php endif; ?>
        </div>
        <h4>Order Summary</h4>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo $content; ?>
            </div>
        </div>
        <a class="btn btn-primary mt-3" href="index.php">Continue Shopping</a>
    </div>
</body>

</html>