<?php
// Simple IPN receiver (debug enhanced)
header('Content-Type: text/plain; charset=UTF-8');

$content = '';
foreach ($_REQUEST as $key => $value) {
    $content .= $key . ' = ' . $value . "\n";
}

// Send email (suppressed errors for shared hosting)
@mail("pferguson@mycolard-art.com", "IPN Received", nl2br($content), "Content-type:text/html");

// Echo debug response so caller gets output
if ($content === '') {
    echo "IPN reachable but no parameters received.";
} else {
    echo "IPN OK. Received parameters:\n\n" . $content;
}

// Optional: write to a log file (comment out if not needed)
$logFile = __DIR__ . '/ipn.log';
@file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . "]\n" . $content . "\n", FILE_APPEND);
//at this point, you have to put any variables you think are awesome into a db.
