<?php
$logFile = 'log.txt';

// Якщо форма надіслана, записуємо дані
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['logText'])) {
    $text = trim($_POST['logText']);
    // Формуємо рядок з часовою міткою
    $entry = "[" . date('Y-m-d H:i:s') . "] " . $text . PHP_EOL;
    
    // Запис у файл (FILE_APPEND додає текст в кінець файлу, а не перезаписує його)
    file_put_contents($logFile, $entry, FILE_APPEND);
    echo "<p style='color: green;'>Текст успішно збережено у $logFile!</p>";
}

echo "<h3>Вміст файлу log.txt:</h3>";

// Читання файлу
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    // Використовуємо htmlspecialchars для безпечного виведення
    echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd;'>" . htmlspecialchars($content) . "</pre>";
} else {
    echo "<p>Файл ще не створено або він порожній.</p>";
}

echo "<br><a href='index.html'>Повернутися на головну</a>";
?>