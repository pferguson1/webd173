<?php
// Simulate PayPal IPN POST to ipnscript.php
$url = 'http://localhost/php-bases/ipnscript.php'; // Change to your actual endpoint if needed

$data = [
    'payment_status' => 'Completed',
    'payer_email' => 'testbuyer@example.com',
    'first_name' => 'Test',
    'last_name' => 'Buyer',
    'contact_phone' => '1234567890',
    'address_street' => '123 Test St',
    'mc_gross' => '99.99',
    'custom' => 'Order123'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];
$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

echo "<pre>";
echo "POSTing to: $url\n\n";
echo "Payload sent:\n" . print_r($data, true) . "\n\n";

if ($result === false) {
    $error = error_get_last();
    echo "Failed to send IPN.\n";
    echo "Error: " . ($error['message'] ?? 'unknown') . "\n";
    if (isset($http_response_header)) {
        echo "HTTP response headers:\n" . implode("\n", $http_response_header) . "\n";
    }
    echo "</pre>";
    exit;
}

echo "Raw endpoint response:\n\n" . htmlspecialchars($result) . "\n";
echo "\nStatus: SUCCESS";
echo "</pre>";
