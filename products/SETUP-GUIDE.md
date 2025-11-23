# ArtShop Products Setup Guide

## Overview

This guide will help you set up the products system for your ArtShop website.

## Prerequisites

- XAMPP installed and running
- MySQL/MariaDB database service active
- Apache server running

## Database Setup

### Step 1: Create the Database

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database called `php_bases` (or use an existing one)

### Step 2: Create Tables

1. In phpMyAdmin, select your database
2. Go to the SQL tab
3. Run the SQL from `create-products.sql` to create the `products` and `orders` tables

### Step 3: Add Art Products

1. Still in phpMyAdmin SQL tab
2. Run the SQL from `add-art-products.sql` to populate your art products

## File Structure

```
products/
├── index.php                 # Main shopping cart and product listing
├── process-order.php         # Handles order processing
├── config.php                # Database and email configuration
├── mailer.php                # Email functionality
├── add-art-products.sql      # SQL to add art products
├── create-products.sql       # SQL to create database tables
└── images/                   # Product images
    ├── face 7.jpg
    ├── bushman.jpg
    ├── thinking-man.jpg
    ├── soul.jpg
    ├── soul-of-a-soldier.jpg
    ├── Black Japanese 21.jpg
    └── black-goddess.jpg
```

## Configuration

### Database Configuration

Edit `config.php` and update these settings if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_bases');  // Your database name
define('DB_USER', 'root');       // Your MySQL username
define('DB_PASS', '');           // Your MySQL password (blank for XAMPP default)
```

### Email Configuration (Optional)

For order confirmation emails, configure SMTP settings in `config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

**Note**: For Gmail, you need to create an "App Password" from your Google Account settings.

## Testing the Products Page

### Test on Static Page

1. Open your browser
2. Navigate to: `http://localhost/artworld/website/products.html`
3. You should see all 7 art products displayed
4. Click "Add to Cart" buttons to test (will redirect to PHP cart)

### Test Shopping Cart

1. Navigate to: `http://localhost/artworld/website/products/index.php`
2. Add products to cart
3. Fill in checkout form
4. Test order submission

## Product Data

The following products are included:

| SKU     | Name                | Price   | Stock |
| ------- | ------------------- | ------- | ----- |
| WARR001 | The Warrior         | $100.00 | 25    |
| BUSH001 | The Bushman         | $90.00  | 20    |
| THIN001 | Thinking Man        | $75.00  | 30    |
| SOUL001 | Beautiful Soul      | $100.00 | 15    |
| PATR001 | An American Patriot | $100.00 | 18    |
| RAIL001 | The Railway Worker  | $100.00 | 22    |
| GODD001 | Black Goddess       | $100.00 | 20    |

## Customization

### Adding New Products

#### Option 1: Via phpMyAdmin

1. Open phpMyAdmin
2. Select `php_bases` database
3. Click on `products` table
4. Click "Insert" tab
5. Fill in product details:
   - product_id: (auto-increment, leave blank)
   - name: Product name
   - sku: Unique product code (e.g., 'PROD001')
   - price: Product price (decimal)
   - image: Image path (e.g., 'images/myimage.jpg')
   - stock: Quantity available

#### Option 2: Via SQL

```sql
INSERT INTO `products` (`name`, `sku`, `price`, `image`, `stock`) VALUES
('New Product', 'NEWP001', '150.00', 'images/new-product.jpg', 50);
```

### Updating Product Images

1. Place image files in `products/images/` folder
2. Update the `image` field in the database with the filename
3. Or update the image path in `products.html` for the static page

### Modifying Prices

```sql
UPDATE `products` SET `price` = '125.00' WHERE `sku` = 'WARR001';
```

### Updating Stock

```sql
UPDATE `products` SET `stock` = 30 WHERE `sku` = 'GODD001';
```

## Troubleshooting

### Products Not Displaying

- Check if XAMPP MySQL is running
- Verify database exists and has data: `SELECT * FROM products;`
- Check PHP error logs in XAMPP control panel

### Images Not Loading

- Verify images exist in `products/images/` folder
- Check file names match database entries (case-sensitive on some systems)
- Verify Apache has read permissions for the images folder

### "Add to Cart" Not Working

- Ensure PHP session is started (check `index.php` has `session_start()`)
- Check browser console for JavaScript errors
- Verify form action URL is correct

### Database Connection Errors

- Confirm MySQL is running in XAMPP
- Check credentials in `config.php`
- Test connection with a simple PHP script:

```php
<?php
$dbh = new PDO("mysql:host=localhost;dbname=php_bases", "root", "");
echo "Connected successfully!";
?>
```

## Integration with Existing Site

The `products.html` page is already integrated with your site design:

- Uses same header/footer as `index.html`
- Matches existing color scheme (#5383d3 primary color)
- Uses same fonts (Manrope)
- Responsive layout with Bootstrap
- Shopping cart icon in navigation

## Next Steps

1. **Set up database** - Run the SQL files
2. **Test static page** - Visit products.html
3. **Test shopping cart** - Visit products/index.php
4. **Configure email** - For order notifications (optional)
5. **Add payment gateway** - Integrate PayPal or Stripe for real payments
6. **Customize products** - Add more artwork as needed

## Support

For questions or issues:

- Check XAMPP error logs
- Review PHP error messages
- Verify database connectivity
- Ensure all file paths are correct

---

**Created for ArtShop Inc. - Fine Art E-commerce Platform**
