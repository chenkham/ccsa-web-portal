<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';

/**
 * Contact Form Mailer Class
 */
class Mailer
{
    /**
     * Sends a contact email.
     *
     * @param string $name
     * @param string $email
     * @param string $message
     * @return array{'success': bool, 'error': string|null}
     */
    public static function send(string $name, string $email, string $message): array
    {
        // Sanitize and validate name
        $name = str_replace(["\r", "\n"], '', $name);
        $name = Security::validateString($name, 1, 100);
        
        // Validate email
        $email = str_replace(["\r", "\n"], '', $email);
        $email = Security::validateEmail($email);

        // Validate message
        $message = Security::validateString($message, 1, 5000);

        if (!$name || !$email || !$message) {
            return ['success' => false, 'error' => 'Invalid input data.'];
        }

        $subject = sprintf("[CCSA Website] Contact from: %s", $name);

        $headers = [
            'From' => MAIL_FROM,
            'Reply-To' => $email,
            'Content-Type' => 'text/plain; charset=UTF-8',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        // Format headers string
        $headersString = '';
        foreach ($headers as $k => $v) {
            $headersString .= "$k: $v\r\n";
        }

        $body = "Name: $name\n";
        $body .= "Email: $email\n\n";
        $body .= "Message:\n$message\n";

        // Prevent email injection in message body
        $body = str_replace("\x00", "", $body);

        if (@mail(MAIL_TO, $subject, $body, $headersString)) {
            return ['success' => true, 'error' => null];
        }

        return ['success' => false, 'error' => 'Failed to send email.'];
    }
}
