<?php
declare(strict_types=1);

// Клієнтське кешування, приклад 1: CSS-файл. Заголовки: Cache-Control, 
// Expires, Content-Type. Браузер не буде запитувати файл повторно протягом 24 годин.
require __DIR__ . '/../src/http_cache.php';

serveCached(__DIR__ . '/../assets/site.css', 'text/css; charset=utf-8', 86400);
