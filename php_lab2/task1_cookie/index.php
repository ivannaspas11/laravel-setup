<?php

// Завдання 1: робота з $_COOKIE

// видалення cookie 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cookie'])) {
    // щоб видалити cookie, ставимо йому час у минулому
    setcookie('username', '', time() - 3600, '/');
    unset($_COOKIE['username']);
    header('Location: index.php');
    exit;
}

// збереження нового імені в cookie 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    if ($username !== '') {
        // 7 днів = 7 * 24 * 60 * 60 секунд
        setcookie('username', $username, time() + 7 * 24 * 60 * 60, '/');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Завдання 1 — $_COOKIE</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 60px auto; }
        input, button { padding: 8px; font-size: 16px; }
        .box { border: 1px solid #ccc; border-radius: 8px; padding: 20px; }
    </style>
</head>
<body>
    <h1>Робота з $_COOKIE</h1>

    <div class="box">
        <?php if (isset($_COOKIE['username'])): ?>
            <p>Вітаємо, <strong><?= htmlspecialchars($_COOKIE['username']) ?></strong>!👋</p>
            <p>Ми пам'ятаємо вас ще 7 днів, бо ваше ім'я збережено в cookie 😀 </p>
            <form method="post">
                <button type="submit" name="delete_cookie">Видалити cookie</button>
            </form>
        <?php else: ?>
            <p>Введіть ваше ім'я😊</p>
            <form method="post">
                <input type="text" name="username" placeholder="Ваше ім'я" required>
                <button type="submit">Зберегти</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>