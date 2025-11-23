<?php

/**
 * Minimal SMTP client using PHP streams with STARTTLS and AUTH LOGIN.
 * Reads config from `config.php` (SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD)
 * Returns verbose debug messages for troubleshooting.
 */
class SmtpMailer
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $timeout = 30;
    public $debug = '';

    public function __construct($host, $port, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    private function readResponse($fp)
    {
        $result = '';
        while (($line = fgets($fp, 515)) !== false) {
            $result .= $line;
            // lines that do not start with 3-digit and '-' are final
            if (preg_match('/^[0-9]{3} (.*)\r?\n$/m', $line)) {
                break;
            }
            // if single-line reply
            if (preg_match('/^[0-9]{3}\s/', $line)) break;
        }
        return $result;
    }

    public function send($to, $subject, $body, $from = null)
    {
        $this->debug = '';
        $remote = $this->host . ':' . $this->port;
        $this->debug .= "Connecting to $remote\n";

        $errno = 0;
        $errstr = '';
        $fp = stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr, $this->timeout);
        if (!$fp) {
            $this->debug .= "Connection failed: $errstr ($errno)\n";
            return false;
        }

        stream_set_timeout($fp, $this->timeout);
        $this->debug .= "Connected.\n";
        $this->debug .= $this->readResponse($fp);

        $this->sendCmd($fp, "EHLO localhost");
        $this->debug .= $this->readResponse($fp);

        // Try STARTTLS if supported
        $this->sendCmd($fp, "STARTTLS");
        $resp = $this->readResponse($fp);
        $this->debug .= $resp;
        if (strpos($resp, '220') === false) {
            // STARTTLS not available — continue but likely insecure
            $this->debug .= "STARTTLS not available or failed.\n";
        } else {
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->debug .= "Failed to enable TLS crypto on socket.\n";
                fclose($fp);
                return false;
            }
            // EHLO again after TLS
            $this->sendCmd($fp, "EHLO localhost");
            $this->debug .= $this->readResponse($fp);
        }

        // AUTH LOGIN
        $this->sendCmd($fp, "AUTH LOGIN");
        $this->debug .= $this->readResponse($fp);
        $this->sendCmd($fp, base64_encode($this->username));
        $this->debug .= $this->readResponse($fp);
        $this->sendCmd($fp, base64_encode($this->password));
        $authResp = $this->readResponse($fp);
        $this->debug .= $authResp;
        if (strpos($authResp, '235') === false) {
            $this->debug .= "Authentication failed.\n";
            fclose($fp);
            return false;
        }

        $fromAddr = $from ?: $this->username;
        $this->sendCmd($fp, "MAIL FROM:<{$fromAddr}>");
        $this->debug .= $this->readResponse($fp);

        $this->sendCmd($fp, "RCPT TO:<{$to}>");
        $this->debug .= $this->readResponse($fp);

        $this->sendCmd($fp, "DATA");
        $this->debug .= $this->readResponse($fp);

        $headers = "From: {$fromAddr}\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $msg = $headers . "\r\n" . $body . "\r\n.";

        fputs($fp, $msg . "\r\n");
        $this->debug .= $this->readResponse($fp);

        $this->sendCmd($fp, "QUIT");
        $this->debug .= $this->readResponse($fp);
        fclose($fp);
        $this->debug .= "Connection closed.\n";
        return true;
    }

    private function sendCmd($fp, $cmd)
    {
        fputs($fp, $cmd . "\r\n");
        $this->debug .= ">> $cmd\n";
    }
}
