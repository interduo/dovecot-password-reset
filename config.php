<?php

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'database_name_here',
        'user' => 'database_user_here',
        'password' => 'database_password_here'
    ],
    'recaptcha' => [
        'secret' => 'google_captcha_secret_here',
        'site-key' => 'google_captcha_site_key_here',
    ],
    'mail' => [
        'host' => 'mx_server_host_here',
        'username' => 'no-reply@company.com',
        'password' => 'email_password',
        'from_email' => 'no-reply@company.com',
        'from_name' => 'Serwer poczty company.com',
        'port' => 587,
        'encryption' => 'tls'
    ],
    'base_url' => 'https://company.com/zapomnialem-hasla/',
];

?>
