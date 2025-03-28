<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Pobranie konfiguracji
$config = require 'config.php';

// Sprawdzenie, czy formularz został przesłany
if ($_GET['action'] == 'get-token') {
    $email = trim($_POST['email'] ?? '');
    $backup_email = trim($_POST['backup_email'] ?? '');
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Walidacja pól
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($backup_email, FILTER_VALIDATE_EMAIL)) {
        die('Niepoprawny adres e-mail.');
    }

    // Weryfikacja Google reCAPTCHA
    $recaptcha_verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$config['recaptcha']['secret']}&response={$recaptcha_response}");
    $recaptcha_result = json_decode($recaptcha_verify);

    if (!$recaptcha_result->success) {
        die('Błąd reCAPTCHA.');
    }

    // Połączenie z bazą danych PostgreSQL
    $db = $config['db'];
    $conn = pg_connect("host={$db['host']} dbname={$db['dbname']} user={$db['user']} password={$db['password']}");

    if (!$conn) {
        die('Błąd połączenia z bazą danych.');
    }

    // Sprawdzenie, czy e-mail istnieje w tabeli "mailbox"
    $result = pg_query_params($conn, 'SELECT 1 FROM mailbox WHERE username = $1 AND employeeid = $2 LIMIT 1', [$email, $backup_email]);

    if (pg_num_rows($result) == 0) {
        die('Error: podana para nie istnieje w systemie.');
    }

    // Generowanie tokena resetującego
    $token = bin2hex(random_bytes(32));
    $expires_at = time()+1800;

    // Zapis tokenu do bazy (tworzymy tabelę reset_tokens jeśli nie istnieje)
    pg_query($conn, "CREATE TABLE IF NOT EXISTS password_change_tokens (
        email TEXT PRIMARY KEY,
        token TEXT NOT NULL,
        expires_at INTEGER NOT NULL
    )");

    // Zapis tokena w bazie PostgreSQL
    pg_query_params($conn, "INSERT INTO password_change_tokens (email, token, expires_at) VALUES ($1, $2, $3) ON CONFLICT (email) DO UPDATE SET token = EXCLUDED.token, expires_at = EXCLUDED.expires_at", [$email, $token, $expires_at]);

    // Wysłanie wiadomości e-mail
    $reset_link = $config['base_url'] . "reset-form.php?token={$token}";

    ob_start();
    include 'email_template.html';
    $email_body = ob_get_clean();
    $email_body = str_replace('{{RESET_LINK}}', $reset_link, $email_body);

    $mail_config = $config['mail'];
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);
        $mail->SMTPSecure = $mail_config['encryption'];
        $mail->Port = $mail_config['port'];

        $mail->setFrom($mail_config['from_email'], $mail_config['from_name']);
        $mail->addAddress($backup_email);

        $mail->isHTML(true);
        $mail->Subject = 'Zmiana hasła skrzynki';
        $mail->Body = $email_body;

        $mail->send();
        echo 'Email resetujący hasło został wysłany.';
    } catch (Exception $e) {
        echo 'Nie udało się wysłać wiadomości. Błąd: ', $mail->ErrorInfo;
    }

    pg_close($conn);
}

if ($_GET['action'] == 'use-token') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password || strlen($password) < 8) {
        die('Hasła nie pasują do siebie lub są zbyt krótkie.');
    }

    // Sprawdzenie tokena w bazie
    $db = $config['db'];
    $conn = pg_connect("host={$db['host']} dbname={$db['dbname']} user={$db['user']} password={$db['password']}");

    if (!$conn) {
        die('Błąd połączenia z bazą danych.');
    }

    $result = pg_query_params($conn, "SELECT email FROM password_change_tokens WHERE token = $1 AND expires_at > extract(epoch from now())", [$token]);

    if (pg_num_rows($result) === 0) {
        die('Token jest niepoprawny lub wygasł.');
    }

    $row = pg_fetch_assoc($result);
    $email = $row['email'];

    // Aktualizacja hasła w bazie
    function generateSSHA512 ($password) {
        $salt = random_bytes(16); // 16 bajtów losowej soli
        $hash = hash('sha512', $password . $salt, true);
        return '{SSHA512}' . base64_encode($hash . $salt);
    }
    $hashed_password = generateSSHA512($password);
    pg_query_params($conn, "UPDATE mailbox SET password = $1 WHERE username = $2", [$hashed_password, $email]);

    // Usuwanie tokena po użyciu
    pg_query_params($conn, "DELETE FROM password_change_tokens WHERE token = $1", [$token]);
    pg_close($conn);

    echo 'Hasło zostało pomyślnie zmienione.';
}
