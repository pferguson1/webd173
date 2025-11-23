@echo off
REM ArtShop Database Setup Script
REM This script sets up your products database in XAMPP

echo =======================================
echo ArtShop Products Database Setup
echo =======================================
echo.

REM Check if MySQL is running
echo Checking if MySQL is running...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running
) else (
    echo [ERROR] MySQL is not running!
    echo Please start MySQL from XAMPP Control Panel first.
    pause
    exit /b 1
)

echo.
echo =======================================
echo Step 1: Creating Database
echo =======================================

REM Create database if it doesn't exist
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS php_bases CHARACTER SET utf8 COLLATE utf8_general_ci;"

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database 'php_bases' ready
) else (
    echo [ERROR] Failed to create database
    pause
    exit /b 1
)

echo.
echo =======================================
echo Step 2: Creating Tables
echo =======================================

REM Create tables
"C:\xampp\mysql\bin\mysql.exe" -u root php_bases < create-products.sql

if %ERRORLEVEL% EQU 0 (
    echo [OK] Tables created successfully
) else (
    echo [ERROR] Failed to create tables
    pause
    exit /b 1
)

echo.
echo =======================================
echo Step 3: Adding Art Products
echo =======================================

REM Add products
"C:\xampp\mysql\bin\mysql.exe" -u root php_bases < add-art-products.sql

if %ERRORLEVEL% EQU 0 (
    echo [OK] Art products added successfully
) else (
    echo [ERROR] Failed to add products
    pause
    exit /b 1
)

echo.
echo =======================================
echo Setup Complete!
echo =======================================
echo.
echo Your products database is now ready!
echo.
echo Next steps:
echo 1. Visit: http://localhost/artworld/website/products.html
echo 2. Test shopping cart: http://localhost/artworld/website/products/index.php
echo.
echo For more information, see SETUP-GUIDE.md
echo.
pause
