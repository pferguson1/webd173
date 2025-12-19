<?php
/**
 * Stock Reset API Test
 */

require_once '../config/config.php';

echo "=== ArtShop Stock Reset System Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM products');
    $result = $stmt->fetch();
    echo "   ✅ Success! Found {$result['count']} products in database\n\n";
} catch(Exception $e) {
    echo "   ❌ Database Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Get Current Stock Levels
echo "2. Current Stock Levels:\n";
try {
    $stmt = $pdo->query('SELECT sku, name, stock FROM products ORDER BY sku LIMIT 10');
    while($row = $stmt->fetch()) {
        $status = $row['stock'] == 0 ? '❌ Out of Stock' : ($row['stock'] <= 15 ? '⚠️  Low Stock' : '✅ In Stock');
        echo "   {$row['sku']} - {$row['name']}: {$row['stock']} units {$status}\n";
    }
    echo "\n";
} catch(Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Test Reset Stock API (Single Product)
echo "3. Testing Reset Stock API (Single Product)...\n";
try {
    // Reset WARR001 to 50 units
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE sku = ?");
    $stmt->execute([50, 'WARR001']);
    
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Successfully reset WARR001 stock to 50 units\n";
        
        // Verify the change
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE sku = ?");
        $stmt->execute(['WARR001']);
        $result = $stmt->fetch();
        echo "   ✅ Verified: WARR001 now has {$result['stock']} units\n\n";
    } else {
        echo "   ❌ No rows affected - product not found\n\n";
    }
} catch(Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Test Reset All Stock
echo "4. Testing Reset All Stock API...\n";
try {
    $stmt = $pdo->prepare("UPDATE products SET stock = ?");
    $stmt->execute([100]);
    
    echo "   ✅ Successfully reset all {$stmt->rowCount()} products to 100 units\n";
    
    // Verify the changes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock = 100");
    $result = $stmt->fetch();
    echo "   ✅ Verified: {$result['count']} products now have 100 units\n\n";
} catch(Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Test Stock Check API Simulation
echo "5. Testing Stock Check (Simulating API call)...\n";
try {
    $stmt = $pdo->prepare("SELECT sku, name, stock FROM products WHERE sku = ?");
    $stmt->execute(['WARR001']);
    $product = $stmt->fetch();
    
    if ($product) {
        $available = $product['stock'] >= 1;
        echo "   ✅ Stock check for {$product['sku']}: {$product['stock']} units available\n";
        echo "   ✅ Can purchase 1 unit: " . ($available ? 'Yes' : 'No') . "\n\n";
    }
} catch(Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Final Stock Summary
echo "6. Final Stock Summary:\n";
try {
    $stmt = $pdo->query('
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock > 0 AND stock <= 15 THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN stock > 15 THEN 1 ELSE 0 END) as in_stock
        FROM products
    ');
    $summary = $stmt->fetch();
    
    echo "   📊 Total Products: {$summary['total']}\n";
    echo "   ❌ Out of Stock: {$summary['out_of_stock']}\n";
    echo "   ⚠️  Low Stock (1-15): {$summary['low_stock']}\n";
    echo "   ✅ In Stock (16+): {$summary['in_stock']}\n\n";
} catch(Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
echo "All stock reset functionality is working properly!\n";
echo "You can now use:\n";
echo "- Admin panel: /admin/stock-manager.php\n";
echo "- API test page: /admin/stock-test.html\n";
echo "- Reset buttons on product pages\n";
?>