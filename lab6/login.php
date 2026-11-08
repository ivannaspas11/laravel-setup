<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: welcome.php');
    exit;
}

$error = '';
$username = '';
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Пошук користувача за іменем (prepared statement)
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_username, $db_hash);
    $found = $stmt->fetch();
    $stmt->close();

    if ($found && check_pass($password, $db_hash)) {   // password_verify()
        session_regenerate_id(true);
        $_SESSION['user_id']  = $id;
        $_SESSION['username'] = $db_username;
        header('Location: welcome.php');
        exit;
    }
    $error = "Невірне ім'я користувача або пароль.";
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Авторизація</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="box">
  <h2>Вхід</h2>
  <?php if ($flash): ?><div class="ok"><?= e($flash) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="login.php">
    <label for="username">Ім'я користувача</label>
    <input type="text" id="username" name="username" value="<?= e($username) ?>" required>

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Увійти</button>
  </form>
  <p class="link">Немає акаунта? <a href="register.php">Зареєструватися</a></p>
</div>
</body>
</html>
