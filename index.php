<?php
$config = require 'config.php';

ob_start();
include 'get-token.html';
$html_body = ob_get_clean();
$html_body = str_replace('{{RECAPTCHA-SITE-KEY}}', $config['recaptcha']['site-key'], $html_body);

echo $html_body;