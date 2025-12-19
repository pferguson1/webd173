<?php
/**
 * System Test & Diagnostics
 * Tests database connection and system components
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>ArtShop System Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .test-item { padding: 15px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; 
               text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔧 ArtShop System Diagnostics</h1>
";

// Test 1: PHP Version
echo "<div class='test-item'>";
echo "<h2>1. PHP Version</h2>";
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<span class='success'>✓ PASS</span> - PHP Version: $phpVersion";
} else {
    echo "<span class='error'>✗ FAIL</span> - PHP Version: $phpVersion (Need 7.4+)";
}
echo "</div>";

// Test 2: Required PHP Extensions
echo "<div class='test-item'>";
echo "<h2>2. PHP Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql', 'mysqli', 'json', 'session'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✓</span> $ext is loaded<br>";
    } else {
        echo "<span class='error'>✗</span> $ext is NOT loaded<br>";
    }
}
echo "</div>";

// Test 3: Config File
echo "<div class='test-item'>";
echo "<h2>3. Configuration File</h2>";
if (file_exists('config.php')) {
    echo "<span class='success'>✓ PASS</span> - config.php exists<br>";
    require_once 'config.php';
    echo "<span class='success'>✓</span> Database: " . DB_NAME . "<br>";
    echo "<span class='success'>✓</span> Host: " . DB_HOST . "<br>";
    echo "<span class='success'>✓</span> User: " . DB_USER . "<br>";
} else {
    echo "<span class='error'>✗ FAIL</span> - config.php not found";
}
echo "</div>";

// Test 4: Database Connection
echo "<div class='test-item'>";
echo "<h2>4. Database Connection</h2>";
try {
    require_once 'config.php';
    
    // Try to connect
    $testPdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS
    );
    echo "<span class='success'>✓ PASS</span> - MySQL connection successful<br>";
    
    // Check if database exists
    $stmt = $testPdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($stmt->rowCount() > 0) {
        echo "<span class='success'>✓</span> Database '" . DB_NAME . "' exists<br>";
        
        // Connect to the database
        $testPdo->query("USE " . DB_NAME);
        
        // Check tables
        $tables = ['users', 'products', 'orders', 'order_items'];
        echo "<h3>Database Tables:</h3>";
        foreach ($tables as $table) {
            $stmt = $testPdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $count = $testPdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "<span class='success'>✓</span> Table '$table' exists ($count rows)<br>";
            } else {
                echo "<span class='error'>✗</span> Table '$table' NOT found<br>";
            }
        }
    } else {
        echo "<span class='warning'>⚠ WARNING</span> - Database '" . DB_NAME . "' does not exist<br>";
        echo "<p><strong>Action Required:</strong> Run <code>setup-database.bat</code> to create database and tables</p>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ FAIL</span> - Database connection failed<br>";
    echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><strong>Common Solutions:</strong></p>";
    echo "<ol>
            <li>Start MySQL from XAMPP Control Panel</li>
            <li>Check MySQL is running on port 3306</li>
            <li>Verify credentials in config.php</li>
          </ol>";
}
echo "</div>";

// Test 5: File Permissions
echo "<div class='test-item'>";
echo "<h2>5. File Structure</h2>";
$files = [
    'products.php' => 'Dynamic Products Page',
    'cart.php' => 'Shopping Cart',
    'login.php' => 'User Login',
    'register.php' => 'User Registration',
    'admin/index.php' => 'Admin Dashboard',
    'api/products.php' => 'Products API',
    'api/orders.php' => 'Orders API',
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "<span class='success'>✓</span> $desc ($file)<br>";
    } else {
        echo "<span class='error'>✗</span> $desc ($file) - NOT FOUND<br>";
    }
}
echo "</div>";

// Test 6: Session
echo "<div class='test-item'>";
echo "<h2>6. Session Support</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<span class='success'>✓ PASS</span> - Session is active<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "<span class='warning'>⚠</span> - Session not started<br>";
}
echo "</div>";

// Summary & Next Steps
echo "<div class='test-item' style='background: #f0f8ff;'>";
echo "<h2>📋 Next Steps</h2>";
echo "<ol>
        <li><strong>Start MySQL:</strong> Open XAMPP Control Panel and start MySQL</li>
        <li><strong>Run Setup:</strong> Execute <code>setup-database.bat</code> in terminal</li>
        <li><strong>Test Pages:</strong>
            <ul>
                <li><a href='products.php' class='btn'>View Products</a></li>
                <li><a href='cart.php' class='btn'>Shopping Cart</a></li>
                <li><a href='admin/' class='btn'>Admin Panel</a></li>
                <li><a href='login.php' class='btn'>Login</a></li>
            </ul>
        </li>
        <li><strong>Admin Login:</strong> Email: admin@artshop.com / Password: admin123</li>
      </ol>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px;'>
        <a href='test-system.php' class='btn'>🔄 Refresh Test</a>
        <a href='products.php' class='btn' style='background: #28a745;'>🛍️ Go to Store</a>
        <a href='INSTALLATION-GUIDE.md' class='btn' style='background: #6c757d;'>📚 View Docs</a>
      </div>";

echo "</body></html>";
?>
