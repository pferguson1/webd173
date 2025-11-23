<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/SmtpMailer.php';

$to = SMTP_USERNAME; // send to self/test inbox
$subject = 'SMTP Test from Simple Cart';
$body = '<h2>SMTP Test</h2><p>This is a test sent by the Simple Cart SMTP test script.</p>';

$mailer = new SmtpMailer(SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD);
$sent = $mailer->send($to, $subject, $body, SMTP_USERNAME);

if ($sent) {
    echo "SUCCESS: Message sent to $to\n";
} else {
    echo "FAILED: Message not sent\n";
}

echo "---- DEBUG OUTPUT ----\n";
echo nl2br(htmlspecialchars($mailer->debug));
