<?php
require_once 'config.php';
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $emailFilter = isset($_GET['email']) ? trim($_GET['email']) : '';
    $sql = 'SELECT order_id, customer_email, order_total, payment_status, created_at FROM orders';
    $params = [];
    if ($emailFilter !== '') {
        $sql .= ' WHERE customer_email LIKE :em';
        $params[':em'] = '%' . $emailFilter . '%';
    }
    $sql .= ' ORDER BY order_id DESC LIMIT 50';
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch items grouped by order_id
    $itemsStmt = $dbh->query('SELECT order_id, sku, product_name, qty, unit_price, line_total FROM order_items ORDER BY id ASC');
    $itemsRaw = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    $items = [];
    foreach ($itemsRaw as $r) {
        $items[$r['order_id']][] = $r;
    }
} catch (Exception $e) {
    die('Admin DB Error: ' . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Orders</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            margin: 25px;
        }

        .order-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .items-table {
            width: 100%;
            margin-top: 10px;
        }

        .items-table th,
        .items-table td {
            font-size: 12px;
            padding: 4px;
            border: 1px solid #ddd;
        }

        .status {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 3px;
            background: #eee;
        }

        .status.pending {
            background: #ffc107;
        }

        .status.completed {
            background: #4caf50;
            color: #fff;
        }

        .badge {
            background: #999;
        }
    </style>
</head>

<body>
    <h1>Orders Admin</h1>
    <form class="form-inline" method="get" style="margin-bottom:20px;">
        <div class="form-group"><label for="email">Filter by email:</label> <input type="text" name="email" id="email" value="<?= htmlspecialchars($emailFilter) ?>" class="form-control" placeholder="customer@example.com"></div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="admin-orders.php" class="btn btn-default">Clear</a>
    </form>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">No orders found.</div>
    <?php else: ?>
        <?php foreach ($orders as $o): $oid = $o['order_id']; ?>
            <div class="order-box">
                <strong>#<?= htmlspecialchars($oid) ?></strong> &mdash; <?= htmlspecialchars($o['customer_email']) ?>
                <span class="status <?= htmlspecialchars(strtolower($o['payment_status'])) ?>"><?= htmlspecialchars($o['payment_status']) ?></span>
                <span class="badge">Total: $<?= htmlspecialchars($o['order_total']) ?></span>
                <div><small><?= htmlspecialchars($o['created_at']) ?></small></div>
                <?php if (isset($items[$oid])): ?>
                    <table class="items-table">
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Line</th>
                        </tr>
                        <?php foreach ($items[$oid] as $it): ?>
                            <tr>
                                <td><?= htmlspecialchars($it['sku']) ?></td>
                                <td><?= htmlspecialchars($it['product_name']) ?></td>
                                <td><?= htmlspecialchars($it['qty']) ?></td>
                                <td>$<?= htmlspecialchars($it['unit_price']) ?></td>
                                <td>$<?= htmlspecialchars($it['line_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <em>No line items</em>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>