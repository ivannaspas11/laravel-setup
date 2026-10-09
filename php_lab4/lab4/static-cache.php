<?php
declare(strict_types=1);

// Серверне кешування через статичну властивість класу (StaticCache).
// У межах одного запиту: 1-й виклик — довгий (sleep 2), 2-й і 3-й — миттєві.
// Після StaticCache::forget() — знову довгий.
// Оновіть сторінку: у новому запиті пам'ять PHP чиста, тож 1-й виклик знову MISS —
// на відміну від файлового кешу та сесії.

require __DIR__ . '/src/StaticCache.php';

final class RateService
{
    public static function getRates(): array
    {
        return StaticCache::remember('rates', 30, static function (): array {
            sleep(2); // важка операція
            return [
                'USD' => round(41.0 + mt_rand(0, 200) / 100, 2),
                'EUR' => round(44.0 + mt_rand(0, 200) / 100, 2),
                'generated_at' => date('H:i:s'),
            ];
        });
    }
}

$log  = [];
$call = static function (string $label) use (&$log): void {
    $missesBefore = StaticCache::misses();
    $t            = microtime(true);
    $rates        = RateService::getRates();
    $ms           = (int) round((microtime(true) - $t) * 1000);

    $log[] = [
        'label' => $label,
        'hit'   => StaticCache::misses() === $missesBefore,
        'ms'    => $ms,
        'rates' => $rates,
    ];
};

$pageStart = microtime(true);

$call('Виклик №1 — кеш порожній');
$call('Виклик №2 — той самий запит');
$call('Виклик №3 — той самий запит');
StaticCache::forget('rates');                    // очищення кешу
$call('Виклик №4 — після StaticCache::forget()');

$total = (int) round((microtime(true) - $pageStart) * 1000);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Кеш у статичній властивості</title>
    <link rel="stylesheet" href="client/style.php">
</head>
<body>
<main>
    <h1>Кеш у статичній властивості класу</h1>
    <div class="card">
        <table>
            <thead><tr><th>Крок</th><th>Результат</th><th>Час, мс</th><th>USD</th><th>EUR</th><th>Згенеровано о</th></tr></thead>
            <tbody>
            <?php foreach ($log as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['label']) ?></td>
                    <td><span class="badge <?= $row['hit'] ? 'hit' : 'miss' ?>"><?= $row['hit'] ? 'HIT' : 'MISS' ?></span></td>
                    <td class="num"><?= $row['ms'] ?></td>
                    <td class="num"><?= $row['rates']['USD'] ?></td>
                    <td class="num"><?= $row['rates']['EUR'] ?></td>
                    <td><?= $row['rates']['generated_at'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p>Загальний час сторінки: <strong><?= $total ?> мс</strong> ·
           hits: <?= StaticCache::hits() ?> · misses: <?= StaticCache::misses() ?></p>
        <p class="muted">
            Кроки 2–3 повернули ті самі значення миттєво (HIT), а після <code>forget()</code> кеш
            перебудовано (нове значення «Згенеровано о»). Статична властивість живе лише протягом
            одного запиту — після оновлення сторінки кеш знову порожній.
        </p>
        <a class="btn" href="static-cache.php">Оновити сторінку</a>
        <a class="btn secondary" href="index.html">← На головну</a>
    </div>
</main>
</body>
</html>
