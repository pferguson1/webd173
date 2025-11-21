<?php
require_once 'config.php';
require_once 'mailer.php';

echo "<h2>Order Email Test</h2>";
echo "<pre>";
echo "SITE_EMAIL: " . SITE_EMAIL . "\n";
echo "FROM_EMAIL: " . FROM_EMAIL . "\n";
echo "SMTP Active: " . (SMTP_ACTIVE ? 'YES' : 'NO') . "\n";
echo "</pre>";

// Test sending to site owner
$ownerSubject = "Test Order #999 - " . SITE_NAME;
$ownerMessage = "<h2>New Order Received!</h2><p>This is a test order notification.</p>";

echo "<h3>Sending to site owner: " . SITE_EMAIL . "</h3>";
$result1 = send_app_mail(SITE_EMAIL, $ownerSubject, $ownerMessage);
echo $result1 ? "<p style='color:green;'>✓ Email to owner sent!</p>" : "<p style='color:red;'>✗ Failed to send to owner</p>";

// Test sending to customer
$customerEmail = 'pferguson8030@gmail.com';
$customerSubject = "Order Confirmation #999 - " . SITE_NAME;
$customerMessage = "<h2>Thank You for Your Order!</h2><p>This is a test confirmation.</p>";

echo "<h3>Sending to customer: " . $customerEmail . "</h3>";
$result2 = send_app_mail($customerEmail, $customerSubject, $customerMessage);
echo $result2 ? "<p style='color:green;'>✓ Email to customer sent!</p>" : "<p style='color:red;'>✗ Failed to send to customer</p>";
