<?php
require_once __DIR__ . '/config.php';

function send_app_mail($to, $subject, $html, $fromEmail = SITE_EMAIL, $fromName = SITE_NAME)
{
    // Basic header
    $headers = "MIME-Version: 1.0\r\n" .
        "Content-type: text/html; charset=UTF-8\r\n" .
        "From: {$fromName} <{$fromEmail}>\r\n";

    // If SMTP not active fall back to mail()
    if (!SMTP_ACTIVE || empty(APP_SMTP_HOST)) {
        return @mail($to, $subject, $html, $headers);
    }

    // Minimal SMTP client using fsockopen (no external libs)
    $secure = strtolower(APP_SMTP_SECURE);
    $host = APP_SMTP_HOST;
    $port = (int)APP_SMTP_PORT;

    $transportHost = ($secure === 'ssl') ? "ssl://{$host}" : $host;
    $fp = @fsockopen($transportHost, $port, $errno, $errstr, 10);
    if (!$fp) {
        if (DEBUG) error_log("SMTP connect failed: $errstr ($errno)");
        return false;
    }
    $read = function () use ($fp) {
        return fgets($fp, 512);
    };
    $write = function ($cmd) use ($fp) {
        fputs($fp, $cmd . "\r\n");
    };

    $read(); // banner
    $write('EHLO localhost');
    // Read multi-line response
    for ($i = 0; $i < 10; $i++) {
        $line = $read();
        if ($line === false || substr($line, 3, 1) !== '-') break;
    }

    if ($secure === 'tls') {
        $write('STARTTLS');
        $tlsResp = $read();
        if (strpos($tlsResp, '220') !== 0) {
            if (DEBUG) error_log('STARTTLS failed: ' . $tlsResp);
            return false;
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            if (DEBUG) error_log('TLS negotiation failed');
            return false;
        }
        $write('EHLO localhost');
        for ($i = 0; $i < 10; $i++) {
            $line = $read();
            if ($line === false || substr($line, 3, 1) !== '-') break;
        }
    }

    // Auth LOGIN
    $write('AUTH LOGIN');
    $read();
    $write(base64_encode(APP_SMTP_USER));
    $read();
    $write(base64_encode(APP_SMTP_PASS));
    $authResp = $read();
    if (strpos($authResp, '235') !== 0) {
        if (DEBUG) error_log('SMTP auth failed: ' . $authResp);
        return false;
    }

    $write('MAIL FROM:<' . $fromEmail . '>');
    $read();
    $write('RCPT TO:<' . $to . '>');
    $rcptResp = $read();
    if (strpos($rcptResp, '250') !== 0) {
        if (DEBUG) error_log('RCPT failed: ' . $rcptResp);
        return false;
    }
    $write('DATA');
    $read();

    $msg  = "From: {$fromName} <{$fromEmail}>\r\n";
    $msg .= "To: <{$to}>\r\n";
    $msg .= "Subject: {$subject}\r\n";
    $msg .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    $msg .= "\r\n" . $html . "\r\n.";
    $write($msg);
    $dataResp = $read();
    $write('QUIT');
    fclose($fp);

    return strpos($dataResp, '250') === 0;
}
