<?php
require_once 'config.php';
require_once 'mailer.php';

echo "<h2>SMTP Test</h2>";
echo "<p>Testing email configuration...</p>";

echo "<pre>";
echo "SMTP_ACTIVE: " . (SMTP_ACTIVE ? 'true' : 'false') . "\n";
echo "APP_SMTP_HOST: " . APP_SMTP_HOST . "\n";
echo "APP_SMTP_PORT: " . APP_SMTP_PORT . "\n";
echo "APP_SMTP_USER: " . APP_SMTP_USER . "\n";
echo "APP_SMTP_PASS: " . (APP_SMTP_PASS ? '***configured***' : 'NOT SET') . "\n";
echo "APP_SMTP_SECURE: " . APP_SMTP_SECURE . "\n";
echo "</pre>";

echo "<h3>Sending test email...</h3>";

$testEmail = 'pferguson8269@outlook.com';
$subject = 'Test Email from PHP Shop';
$message = '<h2>Test Email</h2><p>This is a test email from your PHP shopping cart application.</p><p>Time: ' . date('Y-m-d H:i:s') . '</p>';

$result = send_app_mail($testEmail, $subject, $message);

if ($result) {
    echo "<p style='color:green;'><strong>✓ Email sent successfully!</strong></p>";
    echo "<p>Check your inbox at: $testEmail</p>";
} else {
    echo "<p style='color:red;'><strong>✗ Email failed to send.</strong></p>";
    echo "<p>Check the error log for details.</p>";
}
