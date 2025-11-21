<?php
// Simple diagnostic dump of recent orders and line items
require_once 'config.php';
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $orders = $dbh->query("SELECT order_id, customer_email, order_total, payment_status, created_at FROM orders ORDER BY order_id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $items = $dbh->query("SELECT id, order_id, sku, product_name, qty, unit_price, line_total FROM order_items ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('DB ERROR: ' . htmlspecialchars($e->getMessage()));
}
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Orders Dump</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            font-size: 13px;
        }

        th {
            background: #f2f2f2;
        }

        h2 {
            margin-top: 40px;
        }

        code {
            background: #eee;
            padding: 2px 4px;
        }
    </style>
</head>

<body>
    <h1>Recent Orders</h1>
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['order_id']) ?></td>
                    <td><?= htmlspecialchars($o['customer_email']) ?></td>
                    <td><?= htmlspecialchars($o['order_total']) ?></td>
                    <td><?= htmlspecialchars($o['payment_status']) ?></td>
                    <td><?= htmlspecialchars($o['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <h2>Recent Line Items</h2>
    <?php if (empty($items)): ?>
        <p>No line items found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Order</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Line Total</th>
            </tr>
            <?php foreach ($items as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['id']) ?></td>
                    <td><?= htmlspecialchars($i['order_id']) ?></td>
                    <td><?= htmlspecialchars($i['sku']) ?></td>
                    <td><?= htmlspecialchars($i['product_name']) ?></td>
                    <td><?= htmlspecialchars($i['qty']) ?></td>
                    <td><?= htmlspecialchars($i['unit_price']) ?></td>
                    <td><?= htmlspecialchars($i['line_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <p>Log file: <code>order-insert.log</code></p>
</body>

</html>