<?php
session_start();

// Очищуємо масив значень сесії
$_SESSION = [];

// Видаляємо cookie сесії з браузера
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Знищуємо саму сесію
session_destroy();

// Повертаємося на форму входу
header('Location: index.php');
exit;