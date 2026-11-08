<?php
require 'config.php';

$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || mb_strlen($username) > 50) {
        $errors[] = "Ім'я користувача обов'язкове (до 50 символів).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $errors[] = 'Вкажіть коректну електронну пошту.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Пароль має містити щонайменше 6 символів.';
    }

    if (!$errors) {
        // Перевірка унікальності (prepared statement)
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Користувач з таким іменем або email вже існує.';
        }
        $stmt->close();
    }

    if (!$errors) {
        $hash = hash_pass($password);

        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $hash);

        try {
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash'] = 'Реєстрацію успішно завершено. Тепер увійдіть.';
            header('Location: login.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $errors[] = ($ex->getCode() == 1062)
                ? 'Користувач з таким іменем або email вже існує.'
                : 'Помилка збереження даних.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Реєстрація</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="box">
  <h2>Реєстрація</h2>
  <?php foreach ($errors as $err): ?>
    <div class="err"><?= e($err) ?></div>
  <?php endforeach; ?>
  <form method="POST" action="register.php">
    <label for="username">Ім'я користувача</label>
    <input type="text" id="username" name="username" maxlength="50" value="<?= e($username) ?>" required>

    <label for="email">Електронна пошта</label>
    <input type="email" id="email" name="email" maxlength="100" value="<?= e($email) ?>" required>

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" minlength="6" required>

    <button type="submit">Зареєструватися</button>
  </form>
  <p class="link">Вже є акаунт? <a href="login.php">Увійти</a></p>
</div>
</body>
</html>
