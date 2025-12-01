<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: text/plain');
$res = send_mail('to@example.com', 'Mailtrap test from app', "This is a test via app send_mail() using Mailtrap SMTP.");
echo 'send_mail result: ' . ($res ? 'sent' : 'not sent') . "\n";
echo "\nLast log entries:\n";
$log = file_exists(__DIR__ . '/../storage/emails.log') ? file_get_contents(__DIR__ . '/../storage/emails.log') : '';
echo substr($log, -4000);

