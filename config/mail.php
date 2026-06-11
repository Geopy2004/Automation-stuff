<?php

return [
    'imap_host' => env('MAIL_IMAP_HOST', ''),
    'imap_port' => env('MAIL_IMAP_PORT', '993'),
    'imap_encryption' => env('MAIL_IMAP_ENCRYPTION', 'ssl'),
    'username' => env('MAIL_USERNAME', ''),
    'password' => env('MAIL_APP_PASSWORD', ''),
];
