<?php
// Підключення до MySQL та запуск сесії

$db_host = 'localhost';
$db_user = 'root';      // XAMPP: root
$db_pass = '';          // XAMPP: порожній пароль
$db_name = 'users_db';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Помилка підключення до бази даних. Перевірте налаштування у config.php.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Екранування виводу (захист від XSS)
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Хешування пароля: password_verify() працює лише з хешами у форматі password_hash(),
// тому спочатку застосовується md5(), а результат загортається в password_hash().

function hash_pass(string $password): string {
    return password_hash(md5($password), PASSWORD_DEFAULT);
}

function check_pass(string $password, string $hash): bool {
    return password_verify(md5($password), $hash);
}
