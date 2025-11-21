<?php

/**
 * Simple SMTP Email Class for sending emails via Gmail
 * No external dependencies required
 */
class SimpleMailer
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $error = '';

    public function __construct($host, $port, $username, $password, $from = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->from = $from ?: $username;
    }

    /**
     * Send email via SMTP
     */
    public function send($to, $subject, $body, $isHtml = true)
    {
        // For Windows XAMPP, we'll use a workaround with stream context
        $stream = @stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT
        );

        if (!$stream) {
            $this->error = "Connection failed: $errstr ($errno)";
            return false;
        }

        // Upgrade to TLS
        if (!stream_socket_enable_crypto($stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $this->error = "TLS upgrade failed";
            fclose($stream);
            return false;
        }

        // Read SMTP greeting
        fgets($stream);

        // Send EHLO
        fputs($stream, "EHLO localhost\r\n");
        while (strpos($response = fgets($stream), '250') === false) {
            if (strpos($response, '250-') === false) break;
        }

        // Authenticate
        fputs($stream, "AUTH LOGIN\r\n");
        fgets($stream);

        fputs($stream, base64_encode($this->username) . "\r\n");
        fgets($stream);

        fputs($stream, base64_encode($this->password) . "\r\n");
        $response = fgets($stream);

        if (strpos($response, '235') === false) {
            $this->error = "Authentication failed";
            fclose($stream);
            return false;
        }

        // Send email
        fputs($stream, "MAIL FROM: <{$this->from}>\r\n");
        fgets($stream);

        fputs($stream, "RCPT TO: <{$to}>\r\n");
        fgets($stream);

        fputs($stream, "DATA\r\n");
        fgets($stream);

        $headers = "From: {$this->from}\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        }
        $headers .= "\r\n";

        fputs($stream, $headers . $body . "\r\n.\r\n");
        $response = fgets($stream);

        fputs($stream, "QUIT\r\n");
        fclose($stream);

        return strpos($response, '250') !== false;
    }

    public function getError()
    {
        return $this->error;
    }
}
