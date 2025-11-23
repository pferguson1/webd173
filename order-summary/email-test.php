<?php
// Email configuration test
require_once 'config.php';

echo "<h2>Email Configuration Test</h2>";
echo "<p><strong>SMTP Configuration:</strong></p>";
echo "<ul>";
echo "<li>Host: " . SMTP_HOST . "</li>";
echo "<li>Port: " . SMTP_PORT . "</li>";
echo "<li>Username: " . SMTP_USERNAME . "</li>";
echo "<li>Site Email: " . SITE_EMAIL . "</li>";
echo "</ul>";

echo "<p><strong>Testing email send to test recipient:</strong></p>";

$testTo = 'test@example.com';
$testSubject = 'XAMPP Email Test';
$testMessage = '<h2>Email Test</h2><p>If you received this email, your XAMPP mail configuration is working!</p>';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . SMTP_USERNAME . "\r\n";

$result = @mail($testTo, $testSubject, $testMessage, $headers);

if ($result) {
    echo "<div style='color:green;'><strong>✓ Email function returned success</strong></div>";
    echo "<p>Check your email inbox for the test message.</p>";
} else {
    echo "<div style='color:red;'><strong>✗ Email function failed</strong></div>";
    echo "<p>This means XAMPP mail is not configured. See instructions below.</p>";
}

echo "<hr>";
echo "<h3>To Configure Email in XAMPP:</h3>";
echo "<ol>";
echo "<li>Open: <code>d:\\XAMPP\\php\\php.ini</code></li>";
echo "<li>Find the <code>[mail function]</code> section (around line 1400)</li>";
echo "<li>Update these lines:<br>";
echo "<code>SMTP = smtp.gmail.com<br>";
echo "smtp_port = 587<br>";
echo "sendmail_from = your_email@gmail.com<br>";
echo "sendmail_path = \"\\\"C:\\XAMPP\\sendmail\\sendmail.exe\\\" -t -i\"</code></li>";
echo "<li>Save the file and restart Apache in XAMPP Control Panel</li>";
echo "</ol>";
echo "<p><strong>Note:</strong> You may also need to edit <code>d:\\XAMPP\\sendmail\\sendmail.ini</code> with your Gmail SMTP settings.</p>";
