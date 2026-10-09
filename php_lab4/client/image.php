<?php
declare(strict_types=1);

/**
 * Клієнтське кешування, приклад 2: зображення.
 * Заголовки: Cache-Control, Expires, Content-Type + ETag і Last-Modified.
 *
 *  - Поки не минула доба — браузер бере файл із локального кешу (запиту немає).
 *  - Після F5 або після закінчення max-age браузер надсилає If-None-Match,
 *    а сервер відповідає 304 Not Modified без тіла.
 */
require __DIR__ . '/../src/http_cache.php';

serveCached(__DIR__ . '/../assets/logo.svg', 'image/svg+xml', 86400, true);
