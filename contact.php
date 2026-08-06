<?php
declare(strict_types=1);

$to = "info@luxtradita.com";

function clean_field(string $value): string {
    $value = trim($value);
    // Strip CR/LF to prevent header-injection via mail()
    return str_replace(["\r", "\n"], '', $value);
}

$name     = isset($_POST['name']) ? clean_field($_POST['name']) : '';
$email    = isset($_POST['email']) ? clean_field($_POST['email']) : '';
$message  = isset($_POST['message']) ? trim($_POST['message']) : '';
$honeypot = isset($_POST['website']) ? trim($_POST['website']) : '';

// Honeypot: bots fill every field, real visitors never see this one
if ($honeypot !== '') {
    http_response_code(200);
    exit('Thanks.');
}

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Please fill in all fields with a valid email address.');
}

$subject = "New contact form message from " . $name;

$body  = "Name: $name\n";
$body .= "Email: $email\n\n";
$body .= "Message:\n$message\n";

// From stays on our own domain (avoids spoofing/spam-filter issues on shared hosting);
// Reply-To carries the visitor's address so replying goes straight to them.
$headers  = "From: Lux Tradita Website <noreply@luxtradita.com>\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    header("Location: thank-you.html");
    exit;
} else {
    http_response_code(500);
    exit('Sorry, something went wrong sending your message. Please email us directly at info@luxtradita.com.');
}
