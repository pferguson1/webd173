<?php
require_once 'config.php';

// Get all active products from database
$stmt = $pdo->query("SELECT * FROM products WHERE active = 1 ORDER BY featured DESC, created_at DESC");
$products = $stmt->fetchAll();

// Get unique categories
$categoryStmt = $pdo->query("SELECT DISTINCT category FROM products WHERE active = 1 AND category IS NOT NULL ORDER BY category");
$categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    />
    <title>Products - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="./website_files/bootstrap.min.css" />
    <link rel="stylesheet" href="./website_files/fontawesome-all.min.css" />
    <link rel="stylesheet" href="./website_files/styles.min.css" />
    <link rel="stylesheet" href="./website_files/all.min.css" />
    <link rel="stylesheet" href="./website_files/font-awesome.min.css" />
    <link rel="stylesheet" href="./styles/product.css" />
    <script
      src="https://kit.fontawesome.com/a076d05399.js"
      crossorigin="anonymous"
    ></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
  </head>

  <body>
    <!-- Toast Notification -->
    <div id="toast" class="toast-notification"></div>

    <!-- Header -->
    <header class="bg-dark rounded header">
      <div id="header" class="#logo">
        <div class="logo">
          <img
            class="logo-img rounded-circle object-fit-contain border visible"
            width="188"
            height="96"
            id="logo"
            src="./website_files/Artshop-Logo@1.25x.jpg"
            alt="<?php echo SITE_NAME; ?>"
          />
        </div>
      </div>
      <nav id="menu" class="#nav">
        <ul class="text-center nav-center">
          <li class="me-2 index" id="home">
            <a href="index.html">Home</a>
          </li>
          <li class="ms-0 me-2 #about" id="about">
            <a href="about.html">About</a>
          </li>
          <li class="me-2 #gallery" id="gallery">
            <a href="gallery.html">Gallery</a>
          </li>
          <li id="products" class="products">
            <a href="products.php">Products</a>
          </li>
          <li id="contact" class="contact">
            <a href="contact.html">Contact</a>
          </li>
          <li
            id="fa-shopping-cart"
            class="ms-0 ps-3 me-0 pe-0 shopping-cart cart-icon-container"
          >
            <a href="cart.php" class="cart-link">
              <span
                class="cart-icon iconify"
                data-icon="mdi-light:cart"
              ></span>
              <span id="cart-count">0</span>
            </a>
          </li>
          <li class="bg-dark border rounded-3 search-bar" id="search-bar">
            <div class="search-input">
              <form
                action="https://www.google.com/search"
                method="GET"
                target="_blank"
                id="search-form"
              >
                <input
                  type="text"
                  name="q"
                  placeholder="Search the web..."
                  required
                />
                <button type="submit" class="search-btn">
                  <span
                    class="search-btn-icon iconify"
                    data-icon="arcticons:pixel-search"
                  ></span>
                </button>
              </form>
            </div>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Products Section -->
    <div class="products-section">
      <h2 class="page-title">Our Artwork Collection</h2>
      <div class="container product-catalog">
        <h1 class="text-center mb-4">Product Catalog</h1>
        
        <!-- Category Filter -->
        <div class="category-filter">
          <button class="category-btn active" onclick="filterCategory('all')">
            All
          </button>
          <?php foreach ($categories as $category): ?>
          <button class="category-btn" onclick="filterCategory('<?php echo htmlspecialchars($category); ?>')">
            <?php echo htmlspecialchars($category); ?>
          </button>
          <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <div class="row" id="products-container">
          <?php foreach ($products as $product): ?>
          <div class="col-md-4">
            <div
              class="product-card"
              data-category="<?php echo htmlspecialchars($product['category']); ?>"
              data-stock="<?php echo $product['stock']; ?>"
              data-sku="<?php echo htmlspecialchars($product['sku']); ?>"
            >
              <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
              <h4><?php echo htmlspecialchars($product['name']); ?></h4>
              <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
              <div class="product-details">
                <span class="price"><?php echo formatCurrency($product['price']); ?></span>
                <span class="stock" data-stock="<?php echo $product['stock']; ?>">
                  <?php if ($product['stock'] > 0): ?>
                    In Stock (<?php echo $product['stock']; ?>)
                  <?php else: ?>
                    Out of Stock
                  <?php endif; ?>
                </span>
              </div>
              <button
                class="add-to-cart-btn"
                data-sku="<?php echo htmlspecialchars($product['sku']); ?>"
                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                data-price="<?php echo $product['price']; ?>"
                data-image="<?php echo htmlspecialchars($product['image']); ?>"
                data-stock="<?php echo $product['stock']; ?>"
                <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>
              >
                <i class="fa fa-shopping-cart"></i>
                <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
      <p>&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Load cart count
      function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem("artshop_cart") || "{}");
        const count = Object.keys(cart).length;
        document.getElementById("cart-count").textContent = count;
      }

      // Show toast notification
      function showToast(message) {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.classList.add("show");
        setTimeout(() => {
          toast.classList.remove("show");
        }, 3000);
      }

      // Add to cart functionality
      document.querySelectorAll(".add-to-cart-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const sku = this.dataset.sku;
          const name = this.dataset.name;
          const price = parseFloat(this.dataset.price);
          const image = this.dataset.image;
          const maxStock = parseInt(this.dataset.stock);

          const cart = JSON.parse(localStorage.getItem("artshop_cart") || "{}");

          if (cart[sku]) {
            if (cart[sku].qty >= maxStock) {
              showToast("Maximum stock reached for " + name);
              return;
            }
            cart[sku].qty += 1;
          } else {
            cart[sku] = {
              name: name,
              price: price,
              qty: 1,
              image: image,
            };
          }

          localStorage.setItem("artshop_cart", JSON.stringify(cart));
          updateCartCount();
          showToast(name + " added to cart!");
        });
      });

      // Category filtering
      function filterCategory(category) {
        const products = document.querySelectorAll(".product-card");
        const buttons = document.querySelectorAll(".category-btn");

        buttons.forEach((btn) => btn.classList.remove("active"));
        event.target.classList.add("active");

        products.forEach((product) => {
          if (category === "all" || product.dataset.category === category) {
            product.parentElement.style.display = "block";
          } else {
            product.parentElement.style.display = "none";
          }
        });
      }

      // Initialize on page load
      updateCartCount();
    </script>
  </body>
</html>
