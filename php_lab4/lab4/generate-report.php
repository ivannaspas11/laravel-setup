<?php
declare(strict_types=1);

// Серверне кешування у файл. Імітує генерацію HTML-звіту (sleep(3) + 1000 рядків таблиці).
 // Результат зберігається у cache/report.html на 10 хвилин.
 // generate-report.php          — звичайний запит
 // generate-report.php?clear=1  — спочатку очистити кеш
 
require __DIR__ . '/src/FileCache.php';

const REPORT_KEY = 'report';
const REPORT_TTL = 600; // 10 хвилин

function buildReport(int $rows = 1000): string
{
    sleep(3); // симуляція тривалої обробки

    $names = ['Олена Коваль', 'Андрій Шевченко', 'Марія Бондар', 'Іван Мельник', 'Софія Ткачук',
              'Дмитро Кравчук', 'Наталія Олійник', 'Петро Лисенко', 'Юлія Мороз', 'Богдан Поліщук'];

    $tbody = [];
    for ($i = 1; $i <= $rows; $i++) {
        $tbody[] = sprintf(
            '<tr><td>%d</td><td>%s</td><td class="num">%s</td><td>%s</td></tr>',
            $i,
            htmlspecialchars($names[array_rand($names)], ENT_QUOTES, 'UTF-8'),
            number_format(mt_rand(100, 999999) / 100, 2, '.', ' '),
            date('Y-m-d', time() - mt_rand(0, 365 * 86400))
        );
    }

    return "<table>\n<thead><tr><th>№</th><th>Клієнт</th><th>Сума, грн</th><th>Дата</th></tr></thead>\n<tbody>\n"
        . implode("\n", $tbody)
        . "\n</tbody>\n</table>";
}

$started = microtime(true);
$cache   = new FileCache(__DIR__ . '/cache', 'html');

if (isset($_GET['clear'])) {
    $cache->delete(REPORT_KEY);
}

$table = $cache->get(REPORT_KEY, REPORT_TTL);
$hit   = $table !== null;

if (!$hit) {
    $table = buildReport();
    $cache->set(REPORT_KEY, $table);
}

$elapsedMs = (int) round((microtime(true) - $started) * 1000);
$age       = $cache->age(REPORT_KEY) ?? 0;
$left      = max(0, REPORT_TTL - $age);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache'); // щоб браузерний кеш не заважав демонстрації серверного
header('X-Cache: ' . ($hit ? 'HIT' : 'MISS'));
header('X-Generation-Time-Ms: ' . $elapsedMs);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Звіт (файловий кеш)</title>
    <link rel="stylesheet" href="client/style.php">
</head>
<body>
<main>
    <h1>Звіт (серверне кешування у файл)</h1>
    <div class="card">
        <p>
            Джерело:
            <span class="badge <?= $hit ? 'hit' : 'miss' ?>">
                <?= $hit ? 'CACHE HIT — взято з cache/report.html' : 'CACHE MISS — згенеровано заново (sleep 3 c)' ?>
            </span>
        </p>
        <p>Час виконання скрипта: <strong><?= $elapsedMs ?> мс</strong></p>
        <p class="muted">Вік кешу: <?= $age ?> с · до закінчення TTL: <?= $left ?> с (TTL = <?= REPORT_TTL ?> с)</p>
        <a class="btn" href="generate-report.php">Оновити сторінку</a>
        <a class="btn secondary" href="generate-report.php?clear=1">Очистити кеш і згенерувати</a>
        <a class="btn secondary" href="index.html">← На головну</a>
    </div>
    <div class="card">
        <?= $table ?>
    </div>
</main>
</body>
</html>
