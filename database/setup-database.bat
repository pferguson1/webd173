@echo off
echo ==========================================
echo  ArtShop Inc. - Database Setup Script
echo ==========================================
echo.

echo This script will set up your database...
echo.

REM Check if MySQL is running
echo Checking MySQL connection...
mysql -u root -e "SELECT 1" > nul 2>&1

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Cannot connect to MySQL.
    echo Please ensure:
    echo 1. XAMPP MySQL is running
    echo 2. MySQL is accessible from command line
    echo.
    pause
    exit /b 1
)

echo MySQL is running!
echo.

REM Create database
echo Creating database 'php_bases'...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS php_bases;"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to create database
    pause
    exit /b 1
)

echo Database created successfully!
echo.

REM Import schema
echo Importing database schema and sample data...
mysql -u root php_bases < setup-database.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to import schema
    pause
    exit /b 1
)

echo.
echo ==========================================
echo  DATABASE SETUP COMPLETE!
echo ==========================================
echo.
echo Database: php_bases
echo Tables created:
echo   - users
echo   - products
echo   - orders
echo   - order_items
echo.
echo Sample data inserted:
echo   - 8 Art products
echo   - 1 Admin user
echo.
echo ADMIN LOGIN:
echo   Email: admin@artshop.com
echo   Password: admin123
echo.
echo Next Steps:
echo 1. Open http://localhost/artShop/src/pages/products.php
echo 2. Test the shopping cart
echo 3. Login to admin panel at /admin/
echo.
echo See INSTALLATION-GUIDE.md for complete documentation.
echo.
pause
