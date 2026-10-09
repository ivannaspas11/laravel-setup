<?php
declare(strict_types=1);

// Очищення кешу: clear-cache.php?type=file | session | all. Повертає JSON з результатом.

require __DIR__ . '/src/FileCache.php';

$type   = $_GET['type'] ?? 'all';
$result = [];

if (in_array($type, ['file', 'all'], true)) {
    $cache = new FileCache(__DIR__ . '/cache', 'html');
    $result['file'] = $cache->delete('report') ? 'cleared' : 'already empty';
}

if (in_array($type, ['session', 'all'], true)) {
    session_start();
    $result['session'] = isset($_SESSION['rates_cache']) ? 'cleared' : 'already empty';
    unset($_SESSION['rates_cache']);
    session_write_close();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode($result, JSON_UNESCAPED_UNICODE);
