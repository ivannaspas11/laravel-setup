<?php

// Перевіряємо метод запиту
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: redirect.php');
    exit;
}

// IP-адреса клієнта
$ip = $_SERVER['REMOTE_ADDR'];

// Назва та версія браузера
$browser = $_SERVER['HTTP_USER_AGENT'];

// Назва скрипта
$script = $_SERVER['PHP_SELF'];

// Метод запиту
$method = $_SERVER['REQUEST_METHOD'];

// Шлях до файлу на сервері
$filePath = $_SERVER['SCRIPT_FILENAME'];

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Інформація про сервер</title>
</head>
<body>

    <h1>Інформація про сервер та запит</h1>

    <p>
        <strong>IP-адреса клієнта:</strong>
        <?php echo $ip; ?>
    </p>

    <p>
        <strong>Браузер:</strong>
        <?php echo $browser; ?>
    </p>

    <p>
        <strong>Назва скрипта:</strong>
        <?php echo $script; ?>
    </p>

    <p>
        <strong>Метод запиту:</strong>
        <?php echo $method; ?>
    </p>

    <p>
        <strong>Шлях до файлу на сервері:</strong>
        <?php echo $filePath; ?>
    </p>

</body>
</html>