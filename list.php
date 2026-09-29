<?php
$uploadDir = 'uploads/';

echo "<h2>Список завантажених файлів</h2>";

if (file_exists($uploadDir) && is_dir($uploadDir)) {
    // Скануємо директорію
    $files = scandir($uploadDir);
    $hasFiles = false;

    echo "<ul>";
    foreach ($files as $file) {
        // Пропускаємо поточну та батьківську директорії (. та ..)
        if ($file !== '.' && $file !== '..') {
            $hasFiles = true;
            $filePath = $uploadDir . $file;
            echo "<li style='margin-bottom: 10px;'>";
            echo htmlspecialchars($file);
            echo " — <a href='$filePath' download>Завантажити</a>";
            echo "</li>";
        }
    }
    echo "</ul>";

    if (!$hasFiles) {
        echo "<p>Папка $uploadDir порожня.</p>";
    }
} else {
    echo "<p>Директорія для завантажень ще не створена.</p>";
}

echo "<br><a href='index.html'>Повернутися на головну</a>";
?>