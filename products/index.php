<?php
error_reporting(0);
//Setting session start
session_start();
//var_dump($_SESSION);
$total = 0;

//Database connection — original hardcoded local credentials
$dbh = new PDO("mysql:host=localhost;dbname=php_bases;charset=utf8", "root", "");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//get action string
$action = isset($_GET['action']) ? $_GET['action'] : "";

//Add to cart
if ($action == 'addcart' && $_SERVER['REQUEST_METHOD'] == 'POST') {

  //Finding the product by code
  $query = "SELECT * FROM products WHERE sku=:sku";
  $stmt = $dbh->prepare($query);
  $stmt->bindParam('sku', $_POST['sku']);
  $stmt->execute();
  $product = $stmt->fetch();

  $existingQty = isset($_SESSION['products'][$_POST['sku']]) ? $_SESSION['products'][$_POST['sku']]['qty'] : 0;
  if ($existingQty >= (int)$product['stock']) {
    $_SESSION['flash'] = 'No more stock available for ' . $product['name'];
  } else {
    $currentQty = $existingQty + 1;
    $_SESSION['products'][$_POST['sku']] = array('qty' => $currentQty, 'name' => $product['name'], 'image' => $product['image'], 'price' => $product['price']);
    $_SESSION['flash'] = $product['name'] . ' added to cart.';
  }
  $product = '';
  header("Location:index.php");
}

//Empty All
if ($action == 'emptyall') {
  $_SESSION['products'] = array();
  header("Location:index.php");
}

//Empty one by one
if ($action == 'empty') {
  $sku = $_GET['sku'];
  $products = $_SESSION['products'];
  unset($products[$sku]);
  $_SESSION['products'] = $products;
  header("Location:index.php");
}




//Get all Products
$query = "SELECT * FROM products";
$stmt = $dbh->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Simple Cart</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

</head>

<body>
  <div class="container" style="width:600px;">
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="alert alert-info" style="margin-top:10px;"><?php echo htmlspecialchars($_SESSION['flash']);
                                                              unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['products'])): ?>
      <nav class="navbar navbar-inverse" style="background:#04B745;">
        <div class="container-fluid pull-left" style="width:300px;">
          <div class="navbar-header"> <a class="navbar-brand" href="#" style="color:#FFFFFF;">Shopping Cart</a> </div>
        </div>
        <div class="pull-right" style="margin-top:7px;margin-right:7px;"><a href="index.php?action=emptyall" class="btn btn-info">Empty cart</a></div>
      </nav>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Actions</th>
          </tr>
        </thead>
        <?php foreach ($_SESSION['products'] as $key => $product): ?>
          <tr>
            <td><img src="<?php print $product['image'] ?>" width="50"></td>
            <td><?php print $product['name'] ?></td>
            <td>$<?php print $product['price'] ?></td>
            <td><?php print $product['qty'] ?></td>
            <td><a href="index.php?action=empty&sku=<?php print $key ?>" class="btn btn-info">Delete</a></td>
          </tr>
          <?php $total = $total + $product['price']; ?>
        <?php endforeach; ?>
        <tr>
          <td colspan="5" align="right">
            <h4>Total:$<?php print $total ?></h4>
          </td>
        </tr>
      </table>
    <?php endif; ?>
    <nav class="navbar navbar-inverse" style="background:#04B745;">
      <div class="container-fluid">
        <div class="navbar-header"> <a class="navbar-brand" href="#" style="color:#FFFFFF;">Products</a> </div>
      </div>
    </nav>
    <div class="row">
      <div class="container" style="width:600px;">
        <?php foreach ($products as $product): ?>
          <div class="col-md-4">
            <div class="thumbnail"> <img src="<?php print $product['image'] ?>" alt="Lights">
              <div class="caption">
                <p style="text-align:center;"><?php print $product['name'] ?></p>
                <p style="text-align:center;color:#04B745;"><b>$<?php print $product['price'] ?></b><br><small>Stock: <?php print (int)$product['stock']; ?></small></p>
                <form method="post" action="index.php?action=addcart">
                  <p style="text-align:center;color:#04B745;">
                    <button type="submit" class="btn btn-warning">Add To Cart</button>
                    <input type="hidden" name="sku" value="<?php print $product['sku'] ?>">
                  </p>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (!empty($_SESSION['products'])): ?>
          <div style="margin-top:40px; padding:20px; background:#f9f9f9; border:1px solid #ddd; border-radius:5px;">
            <h4 style="margin-bottom:20px;">Checkout</h4>
            <form method="post" action="process-order.php">
              <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone">
              </div>
              <div class="form-group">
                <label for="address">Shipping Address:</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
              </div>
              <p style="text-align:center;color:#04B745;">
                <button type="submit" class="btn btn-success btn-lg">Complete Order</button>
              </p>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>

</html>