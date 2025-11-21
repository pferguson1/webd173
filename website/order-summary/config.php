<?php
// Email Configuration - SendGrid SMTP
define('SENDGRID_API_KEY', 'your_sendgrid_api_key');  // Get from https://app.sendgrid.com/settings/api_keys
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'apikey');  // Always 'apikey' for SendGrid
define('SMTP_PASSWORD', SENDGRID_API_KEY);  // Your SendGrid API key
define('SITE_EMAIL', 'pferguson@mycolard-art.com');  // Where to send order notifications
define('FROM_EMAIL', 'pferguson@mycolard-art.com');  // From address for emails (change if you have verified sender domain)
define('SITE_NAME', 'Simple Cart Shop');

// Database configuration
// On your hosting (panel.data-center.com) set these values to your host DB credentials.
// You can also set environment variables DB_HOST, DB_NAME, DB_USER, DB_PASS and they will be used.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
// Local default DB name (use 'php_bases' for XAMPP; override via env on host)
define('DB_NAME', getenv('DB_NAME') ?: 'php_bases');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Debug flag (set false for production)
define('DEBUG', getenv('DEBUG') ? (strtolower(getenv('DEBUG')) === 'true') : true);

// SMTP configuration (fallback to PHP mail() if not fully set)
// Override via environment variables SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE
define('SMTP_ACTIVE', getenv('SMTP_HOST') && getenv('SMTP_USER') && getenv('SMTP_PASS') ? true : (getenv('SMTP_HOST') ?: 'smtp.gmail.com') && (getenv('SMTP_USER') ?: 'pferguson8030@gmail.com') && (getenv('SMTP_PASS') ?: 'rjtwlfmtfbsnmhjh'));
define('APP_SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('APP_SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('APP_SMTP_USER', getenv('SMTP_USER') ?: 'pferguson8030@gmail.com');
define('APP_SMTP_PASS', getenv('SMTP_PASS') ?: 'rjtwlfmtfbsnmhjh');
define('APP_SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); // tls or ssl
/**
 * IMPORTANT: Getting SendGrid API Key
 * 1. Sign up at https://sendgrid.com (free tier available)
 * 2. Go to Settings > API Keys: https://app.sendgrid.com/settings/api_keys
 * 3. Create a new API key with "Mail Send" permissions
 * 4. Copy the key and paste it in SENDGRID_API_KEY above
 * 5. SendGrid SMTP uses 'apikey' as username and your API key as password
 */
