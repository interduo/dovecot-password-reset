<?php
$config = require 'config.php';

ob_start();
include 'reset-form.html';
$html_body = ob_get_clean();

$html_body = str_replace(
    ['{{PASSTOKEN}}', '{{RECAPTCHA-SITE-KEY}}'],
    [htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES, 'UTF-8'), $config['recaptcha']['site-key']],
    $html_body
);

echo $html_body;