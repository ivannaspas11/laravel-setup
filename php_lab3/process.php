<?php
$uploadDir = 'uploads/';

// Створюємо папку, якщо її не існує
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadedFile'])) {
    $file = $_FILES['uploadedFile'];

    // Перевірка, чи завантажено файл через HTTP POST
    if (is_uploaded_file($file['tmp_name'])) {
        
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileType = $file['type'];
        
        $maxSize = 2 * 1024 * 1024; // 2 МБ у байтах
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Перевірка розміру
        if ($fileSize > $maxSize) {
            die("Помилка: Розмір файлу перевищує 2 МБ. <a href='index.html'>Назад</a>");
        }

        // Перевірка типу
        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Помилка: Дозволено лише файли форматів JPG, JPEG та PNG. <a href='index.html'>Назад</a>");
        }

        $targetPath = $uploadDir . $fileName;

        // Перевірка наявності файлу та додавання унікального суфікса
        if (file_exists($targetPath)) {
            $uniqueSuffix = time() . '_' . rand(100, 999);
            $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = $fileNameWithoutExt . '_' . $uniqueSuffix . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            echo "<p style='color: orange;'>Файл з таким ім'ям вже існував. Його збережено як: <b>$fileName</b></p>";
        }

        // Збереження файлу
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $sizeKB = round($fileSize / 1024, 2);
            echo "<h3 style='color: green;'>Файл успішно завантажено!</h3>";
            echo "<ul>";
            echo "<li><b>Ім'я файлу:</b> $fileName</li>";
            echo "<li><b>Тип:</b> $fileType</li>";
            echo "<li><b>Розмір:</b> $sizeKB КБ</li>";
            echo "</ul>";
            
            // Атрибут download дозволяє користувачу зберегти файл назад
            echo "<a href='$targetPath' download>Завантажити файл назад</a><br><br>";
        } else {
            echo "<p style='color: red;'>Сталася помилка при збереженні файлу.</p>";
        }
    } else {
        echo "<p style='color: red;'>Помилка: Файл не був успішно завантажений.</p>";
    }
} else {
    echo "Некоректний запит.";
}

echo "<br><br><a href='index.html'>Повернутися на головну</a>";
?>