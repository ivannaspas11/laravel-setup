<?php

// Завдання 5: автоматичне завершення сесії після 5 хвилин неактивності

session_start();

// Перевірка часу останньої активності
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 300) {
        // Якщо користувач був неактивний більше 5 хвилин
        session_unset();
        session_destroy();

        // Починаємо нову сесію
        session_start();
    }
}

// Оновлюємо час останньої активності
$_SESSION['last_activity'] = time();


// Ініціалізація кошика
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// додати товар у кошик (сесія) 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product = trim($_POST['product']);
    if ($product !== '') {
        $_SESSION['cart'][] = $product;
    }
    header('Location: index.php');
    exit;
}

// оформити покупку: перенести товари із сесії в cookie 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $previous = [];
    if (isset($_COOKIE['previous_purchases'])) {
        $decoded = json_decode($_COOKIE['previous_purchases'], true);
        if (is_array($decoded)) {
            $previous = $decoded;
        }
    }
// демонстрація спільної роботи $_SESSION та $_COOKIE
    $previous = array_merge($previous, $_SESSION['cart']);
    // зберігаємо список товарів у cookie на 30 днів (як приклад "довгої пам'яті")
    setcookie('previous_purchases', json_encode($previous), time() + 30 * 24 * 60 * 60, '/');

    $_SESSION['cart'] = [];
    header('Location: index.php');
    exit;
}

// очистити історію покупок (cookie) 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_history'])) {
    setcookie('previous_purchases', '', time() - 3600, '/');
    unset($_COOKIE['previous_purchases']);
    header('Location: index.php');
    exit;
}

// зчитуємо історію покупок з cookie для відображення
$previousPurchases = [];
if (isset($_COOKIE['previous_purchases'])) {
    $decoded = json_decode($_COOKIE['previous_purchases'], true);
    if (is_array($decoded)) {
        $previousPurchases = $decoded;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Завдання 4 — Кошик покупок</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; }
        .box { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        ul { padding-left: 20px; }
        button { padding: 6px 12px; }
    </style>
</head>
<body>
    <h1>Простий кошик покупок🧺</h1>

    <div class="box">
        <h2>Поточний кошик (сесія)</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Кошик порожній😿 .</p>
        <?php else: ?>
            <ul>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
            <form method="post" style="display:inline">
                <button type="submit" name="checkout">Оформити покупку✅</button>
            </form>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="product" placeholder="Назва товару" required>
            <button type="submit" name="add_product">Додати в кошик➕</button>
        </form>
    </div>

    <div class="box">
        <h2>Попередні покупки (cookie)</h2>
        <?php if (empty($previousPurchases)): ?>
            <p>Історія покупок порожня🙆‍♀️.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($previousPurchases as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
            <form method="post">
                <button type="submit" name="clear_history">Очистити історію</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>