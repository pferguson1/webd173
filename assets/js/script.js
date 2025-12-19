// Artwork Detail Page JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // Initialize price display
  updatePrice();

  // Handle size selection changes
  const sizeOptions = document.querySelectorAll('input[name="size"]');
  sizeOptions.forEach((option) => {
    option.addEventListener("change", function () {
      updatePrice();
      updateSizeSelection();
    });
  });

  // Handle Add to Cart button
  const addToCartBtn = document.querySelector(".add-to-cart");
  addToCartBtn.addEventListener("click", function () {
    handleAddToCart();
  });

  // Handle Add to Wishlist button
  const addToWishlistBtn = document.querySelector(".add-to-wishlist");
  addToWishlistBtn.addEventListener("click", function () {
    handleAddToWishlist();
  });

  // Handle Back to Gallery navigation
  const backLink = document.querySelector(".back-link");
  backLink.addEventListener("click", function (e) {
    e.preventDefault();
    handleBackToGallery();
  });
});

// Update price based on selected size
function updatePrice() {
  const selectedOption = document.querySelector('input[name="size"]:checked');
  const currentPriceElement = document.getElementById("current-price");

  if (selectedOption && currentPriceElement) {
    const price = selectedOption.getAttribute("data-price");
    currentPriceElement.textContent = `$${price}`;

    // Add animation effect
    currentPriceElement.style.transform = "scale(1.1)";
    setTimeout(() => {
      currentPriceElement.style.transform = "scale(1)";
    }, 200);
  }
}

// Update visual selection for size options
function updateSizeSelection() {
  const sizeCards = document.querySelectorAll(".size-card");
  sizeCards.forEach((card) => {
    const radio = card.querySelector('input[type="radio"]');
    const sizeInfo = card.querySelector(".size-info");
    const dimensions = card.querySelector(".size-dimensions");
    const price = card.querySelector(".size-price");

    if (radio.checked) {
      sizeInfo.style.borderColor = "#007bff";
      sizeInfo.style.backgroundColor = "#f8f9ff";
      dimensions.style.color = "#007bff";
      price.style.color = "#007bff";
    } else {
      sizeInfo.style.borderColor = "#e9ecef";
      sizeInfo.style.backgroundColor = "#fff";
      dimensions.style.color = "#495057";
      price.style.color = "#dc3545";
    }
  });
}

// Handle Add to Cart functionality
function handleAddToCart() {
  const selectedSize = document.querySelector('input[name="size"]:checked');
  const artworkTitle = document.querySelector(".artwork-title").textContent;
  const artist = document.querySelector(".artist-name").textContent;
  const price = document.getElementById("current-price").textContent;

  if (!selectedSize) {
    showNotification("Please select a print size first.", "warning");
    return;
  }

  // Simulate adding to cart
  const cartItem = {
    title: artworkTitle,
    artist: artist,
    size: selectedSize.value,
    price: price,
    timestamp: new Date(),
  };

  // Store in localStorage (in a real app, this would be sent to a server)
  let cart = JSON.parse(localStorage.getItem("artCart") || "[]");
  cart.push(cartItem);
  localStorage.setItem("artCart", JSON.stringify(cart));

  // Show success message
  showNotification(`"${artworkTitle}" added to cart successfully!`, "success");

  // Update cart count if it exists
  updateCartCount();

  // Add visual feedback to button
  const button = document.querySelector(".add-to-cart");
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-check"></i> Added!';
  button.style.backgroundColor = "#28a745";

  setTimeout(() => {
    button.innerHTML = originalText;
    button.style.backgroundColor = "#007bff";
  }, 2000);
}

// Handle Add to Wishlist functionality
function handleAddToWishlist() {
  const artworkTitle = document.querySelector(".artwork-title").textContent;
  const artist = document.querySelector(".artist-name").textContent;
  const button = document.querySelector(".add-to-wishlist");
  const icon = button.querySelector("i");

  // Check if already in wishlist
  let wishlist = JSON.parse(localStorage.getItem("artWishlist") || "[]");
  const isInWishlist = wishlist.some(
    (item) => item.title === artworkTitle && item.artist === artist
  );

  if (isInWishlist) {
    // Remove from wishlist
    wishlist = wishlist.filter(
      (item) => !(item.title === artworkTitle && item.artist === artist)
    );
    localStorage.setItem("artWishlist", JSON.stringify(wishlist));

    // Update button appearance
    icon.classList.remove("fas");
    icon.classList.add("far");
    button.innerHTML = '<i class="far fa-heart"></i> Add to Wishlist';
    showNotification("Removed from wishlist", "info");
  } else {
    // Add to wishlist
    const wishlistItem = {
      title: artworkTitle,
      artist: artist,
      image: document.querySelector(".artwork-image").src,
      timestamp: new Date(),
    };

    wishlist.push(wishlistItem);
    localStorage.setItem("artWishlist", JSON.stringify(wishlist));

    // Update button appearance
    icon.classList.remove("far");
    icon.classList.add("fas");
    button.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
    button.style.color = "#dc3545";
    showNotification("Added to wishlist!", "success");
  }
}

// Handle Back to Gallery navigation
function handleBackToGallery() {
  // Navigate back to the index.html page
  window.location.href = "index.html";
}

// Show notification messages
function showNotification(message, type = "info") {
  // Remove existing notifications
  const existingNotification = document.querySelector(".notification");
  if (existingNotification) {
    existingNotification.remove();
  }

  // Create notification element
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

  // Add styles
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
        font-weight: 500;
    `;

  // Set colors based on type
  const colors = {
    success: { bg: "#d4edda", color: "#155724", border: "#c3e6cb" },
    warning: { bg: "#fff3cd", color: "#856404", border: "#ffeaa7" },
    error: { bg: "#f8d7da", color: "#721c24", border: "#f5c6cb" },
    info: { bg: "#d1ecf1", color: "#0c5460", border: "#bee5eb" },
  };

  const typeColors = colors[type] || colors.info;
  notification.style.backgroundColor = typeColors.bg;
  notification.style.color = typeColors.color;
  notification.style.border = `1px solid ${typeColors.border}`;

  // Add to document
  document.body.appendChild(notification);

  // Handle close button
  const closeBtn = notification.querySelector(".notification-close");
  closeBtn.addEventListener("click", () => {
    notification.remove();
  });

  // Auto remove after 3 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 3000);
}

// Update cart count (if cart counter exists in navigation)
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("artCart") || "[]");
  const cartCountElement = document.querySelector(".cart-count");

  if (cartCountElement) {
    cartCountElement.textContent = cart.length;
    cartCountElement.style.display = cart.length > 0 ? "inline" : "none";
  }
}

// Initialize wishlist state on page load
function initializeWishlistState() {
  const artworkTitle = document.querySelector(".artwork-title").textContent;
  const artist = document.querySelector(".artist-name").textContent;
  const wishlist = JSON.parse(localStorage.getItem("artWishlist") || "[]");
  const button = document.querySelector(".add-to-wishlist");
  const icon = button.querySelector("i");

  const isInWishlist = wishlist.some(
    (item) => item.title === artworkTitle && item.artist === artist
  );

  if (isInWishlist) {
    icon.classList.remove("far");
    icon.classList.add("fas");
    button.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
    button.style.color = "#dc3545";
  }
}

// Add CSS for animations
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notification-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        opacity: 0.7;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
    
    .btn {
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(style);

// Initialize wishlist state when page loads
document.addEventListener("DOMContentLoaded", function () {
  initializeWishlistState();
});
