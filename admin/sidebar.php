<!-- Admin Sidebar Navigation -->
<div class="col-md-2 sidebar">
    <h3 class="text-center mb-4"><?php echo SITE_NAME; ?></h3>
    <nav>
        <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'class="active"' : ''; ?>>
            <i class="fa fa-dashboard"></i> Dashboard
        </a>
        <a href="products.php" <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'class="active"' : ''; ?>>
            <i class="fa fa-shopping-bag"></i> Products
        </a>
        <a href="orders.php" <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'class="active"' : ''; ?>>
            <i class="fa fa-shopping-cart"></i> Orders
        </a>
        <a href="users.php" <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'class="active"' : ''; ?>>
            <i class="fa fa-users"></i> Users
        </a>
        <hr style="border-color: #555;">
        <a href="../products.php"><i class="fa fa-store"></i> View Store</a>
        <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    </nav>
</div>

<style>
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
    .content-area {
        padding: 30px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }
    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
</style>
