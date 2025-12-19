<?php
require_once 'config.php';

// Handle AJAX requests for cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'sync_cart':
            // Sync localStorage cart with session
            if (isset($_POST['cart'])) {
                $_SESSION['cart'] = json_decode($_POST['cart'], true);
                echo json_encode(['success' => true]);
            }
            exit;
            
        case 'get_cart':
            echo json_encode($_SESSION['cart'] ?? []);
            exit;
    }
}

// Get cart from session or initialize empty
$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    />
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link rel="stylesheet" href="styles/cart.css" />
  </head>

  <body>
    <div class="container cart-container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Shopping Cart</h1>
        <div>
          <button
            class="btn btn-outline-danger mr-2"
            onclick="clearCart()"
            id="clearCartBtn"
          >
            <i class="fa fa-trash"></i> Clear Cart
          </button>
          <a href="products.php" class="btn btn-outline-primary">
            <i class="fa fa-arrow-left"></i> Back to Products
          </a>
        </div>
      </div>

      <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
          <div id="cartItems">
            <!-- Cart items will be dynamically loaded here -->
          </div>

          <div id="emptyCart" class="empty-cart" style="display: none">
            <i class="fa fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Add some items to get started!</p>
            <a href="products.php" class="btn btn-primary mt-3"
              >Continue Shopping</a
            >
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
          <div class="cart-summary">
            <h4 class="mb-4">Order Summary</h4>

            <div class="summary-row">
              <span>Subtotal:</span>
              <span id="subtotal">$0.00</span>
            </div>

            <div class="summary-row">
              <span>Shipping:</span>
              <span id="shipping">$0.00</span>
            </div>

            <div class="summary-row">
              <span>Tax (0%):</span>
              <span id="tax">$0.00</span>
            </div>

            <div class="summary-row total">
              <span>Total:</span>
              <span id="total">$0.00</span>
            </div>

            <button
              class="btn btn-primary btn-block btn-lg mt-4"
              onclick="proceedToCheckout()"
            >
              <i class="fa fa-lock"></i> Proceed to Checkout
            </button>

            <button
              class="btn btn-outline-secondary btn-block mt-2"
              onclick="continueShopping()"
            >
              Continue Shopping
            </button>

            <button
              class="btn btn-outline-danger btn-block mt-2"
              onclick="clearCart()"
              id="clearCartBtnSummary"
            >
              <i class="fa fa-trash"></i> Clear Cart
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      let cart = {};

      // Load cart from localStorage
      function loadCart() {
        const cartData = localStorage.getItem("artshop_cart");
        cart = cartData ? JSON.parse(cartData) : {};
        
        // Sync with session
        $.post('cart.php', {
          action: 'sync_cart',
          cart: JSON.stringify(cart)
        });
        
        renderCart();
      }

      // Save cart to localStorage
      function saveCart() {
        localStorage.setItem("artshop_cart", JSON.stringify(cart));
        
        // Sync with session
        $.post('cart.php', {
          action: 'sync_cart',
          cart: JSON.stringify(cart)
        });
      }

      // Render cart items
      function renderCart() {
        const cartItemsDiv = document.getElementById("cartItems");
        const emptyCartDiv = document.getElementById("emptyCart");

        if (Object.keys(cart).length === 0) {
          cartItemsDiv.innerHTML = "";
          emptyCartDiv.style.display = "block";
          updateSummary();
          return;
        }

        emptyCartDiv.style.display = "none";
        let html = "";

        for (let sku in cart) {
          const item = cart[sku];
          html += `
            <div class="cart-item">
              <div class="d-flex align-items-center">
                <img src="${item.image}" alt="${item.name}" class="mr-3" />
                <div class="item-details">
                  <h5>${item.name}</h5>
                  <p class="text-muted">SKU: ${sku}</p>
                  <p class="font-weight-bold">$${item.price.toFixed(2)}</p>
                </div>
                <div class="quantity-control ml-auto">
                  <button
                    class="btn btn-sm btn-outline-secondary"
                    onclick="updateQuantity('${sku}', -1)"
                  >
                    <i class="fa fa-minus"></i>
                  </button>
                  <input
                    type="number"
                    class="form-control form-control-sm d-inline-block mx-2"
                    value="${item.qty}"
                    min="1"
                    onchange="setQuantity('${sku}', this.value)"
                    style="width: 60px;"
                  />
                  <button
                    class="btn btn-sm btn-outline-secondary"
                    onclick="updateQuantity('${sku}', 1)"
                  >
                    <i class="fa fa-plus"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger ml-2"
                    onclick="removeItem('${sku}')"
                  >
                    <i class="fa fa-trash"></i> Remove
                  </button>
                </div>
              </div>
            </div>
          `;
        }

        cartItemsDiv.innerHTML = html;
        updateSummary();
      }

      // Update quantity
      function updateQuantity(sku, change) {
        if (cart[sku]) {
          cart[sku].qty += change;
          if (cart[sku].qty <= 0) {
            delete cart[sku];
          }
          saveCart();
          renderCart();
        }
      }

      // Set quantity directly
      function setQuantity(sku, qty) {
        qty = parseInt(qty);
        if (qty <= 0) {
          delete cart[sku];
        } else {
          cart[sku].qty = qty;
        }
        saveCart();
        renderCart();
      }

      // Remove item
      function removeItem(sku) {
        delete cart[sku];
        saveCart();
        renderCart();
      }

      // Clear cart
      function clearCart() {
        if (confirm("Are you sure you want to clear your cart?")) {
          cart = {};
          saveCart();
          renderCart();
        }
      }

      // Update summary
      function updateSummary() {
        let subtotal = 0;
        for (let sku in cart) {
          subtotal += cart[sku].price * cart[sku].qty;
        }

        const shipping = subtotal > 0 ? 0 : 0; // Free shipping
        const tax = 0;
        const total = subtotal + shipping + tax;

        document.getElementById("subtotal").textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById("shipping").textContent = `$${shipping.toFixed(2)}`;
        document.getElementById("tax").textContent = `$${tax.toFixed(2)}`;
        document.getElementById("total").textContent = `$${total.toFixed(2)}`;
      }

      // Proceed to checkout
      function proceedToCheckout() {
        if (Object.keys(cart).length === 0) {
          alert("Your cart is empty!");
          return;
        }

        // Save cart data and redirect to checkout
        saveCart();
        const total = parseFloat(
          document.getElementById("total").textContent.replace("$", "")
        );
        localStorage.setItem("checkoutTotal", total.toFixed(2));

        window.location.href = "credit-card/index.html";
      }

      // Continue shopping
      function continueShopping() {
        window.location.href = "products.php";
      }

      // Initialize cart on page load
      window.onload = function () {
        loadCart();
      };
    </script>
  </body>
</html>
