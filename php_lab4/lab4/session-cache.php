<?php
declare(strict_types=1);

// Серверне кешування у змінній сесії ($_SESSION).
// Курси валют "обчислюються" 2 секунди, після чого зберігаються в сесії на 60 секунд.
// Кеш індивідуальний для кожного користувача (сесії).
 // session-cache.php               — HTML-сторінка
 // session-cache.php?format=json   — JSON 
 // session-cache.php?clear=1       — очистити кеш у сесії
 
const CACHE_KEY = 'rates_cache';
const CACHE_TTL = 60;

function calculateRates(): array
{
    sleep(2); // симуляція довгого обчислення / запиту до зовнішнього API

    return [
        'USD' => round(41.0 + mt_rand(0, 200) / 100, 2),
        'EUR' => round(44.0 + mt_rand(0, 200) / 100, 2),
        'PLN' => round(10.0 + mt_rand(0, 100) / 100, 2),
        'GBP' => round(52.0 + mt_rand(0, 200) / 100, 2),
    ];
}

session_start();
$started = microtime(true);

if (isset($_GET['clear'])) {
    unset($_SESSION[CACHE_KEY]);
}

$entry = $_SESSION[CACHE_KEY] ?? null;
$hit   = is_array($entry) && (time() - $entry['created']) < CACHE_TTL;

if ($hit) {
    $rates   = $entry['data'];
    $created = $entry['created'];
} else {
    $rates   = calculateRates();
    $created = time();
    $_SESSION[CACHE_KEY] = ['created' => $created, 'data' => $rates];
}

session_write_close(); // знімаємо блокування сесії

$elapsedMs = (int) round((microtime(true) - $started) * 1000);
$age       = time() - $created;

header('Cache-Control: no-store');
header('X-Cache: ' . ($hit ? 'HIT' : 'MISS'));
header('X-Generation-Time-Ms: ' . $elapsedMs);

if (($_GET['format'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'source'     => $hit ? 'HIT' : 'MISS',
        'elapsed_ms' => $elapsedMs,
        'cache_age'  => $age,
        'ttl'        => CACHE_TTL,
        'rates'      => $rates,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Кеш у сесії</title>
    <link rel="stylesheet" href="client/style.php">
</head>
<body>
<main>
    <h1>Курси валют (кеш у $_SESSION)</h1>
    <div class="card">
        <p>
            Джерело:
            <span class="badge <?= $hit ? 'hit' : 'miss' ?>">
                <?= $hit ? 'CACHE HIT — взято з сесії' : 'CACHE MISS — обчислено заново (sleep 2 c)' ?>
            </span>
        </p>
        <p>Час виконання: <strong><?= $elapsedMs ?> мс</strong></p>
        <p class="muted">Вік кешу: <?= $age ?> с з <?= CACHE_TTL ?> с (TTL)</p>
        <table>
            <thead><tr><th>Валюта</th><th>Курс, грн</th></tr></thead>
            <tbody>
            <?php foreach ($rates as $code => $value): ?>
                <tr><td><?= htmlspecialchars($code) ?></td><td class="num"><?= number_format($value, 2) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <a class="btn" href="session-cache.php">Оновити сторінку</a>
            <a class="btn secondary" href="session-cache.php?clear=1">Очистити кеш</a>
            <a class="btn secondary" href="index.html">← На головну</a>
        </p>
    </div>
</main>
</body>
</html>
