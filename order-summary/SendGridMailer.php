<?php

/**
 * SendGrid SMTP Wrapper - uses SmtpMailer with SendGrid SMTP credentials
 * Simplifies sending emails via SendGrid without needing an API client library
 */
require_once __DIR__ . '/SmtpMailer.php';

class SendGridMailer
{
    private $mailer;
    public $debug = '';

    public function __construct($apiKey)
    {
        // SendGrid SMTP: username is always 'apikey', password is the API key
        $this->mailer = new SmtpMailer(SMTP_HOST, SMTP_PORT, SMTP_USERNAME, $apiKey);
    }

    public function send($to, $subject, $body, $from = null)
    {
        $result = $this->mailer->send($to, $subject, $body, $from);
        $this->debug = $this->mailer->debug;
        return $result;
    }

    public function getDebug()
    {
        return $this->debug;
    }
}
