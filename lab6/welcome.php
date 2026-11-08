<?php
require 'config.php';

// Захищена сторінка: доступна лише після входу
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username, $email);
$ok = $stmt->fetch();
$stmt->close();

if (!$ok) {   // користувача видалено з БД
    header('Location: logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="box">
  <h2>Welcome, <?= e($username) ?>!</h2>
  <p>Ви успішно авторизовані.</p>
  <p><b>Email:</b> <?= e($email) ?></p>
  <p><b>ID користувача:</b> <?= (int)$_SESSION['user_id'] ?></p>
  <a class="btn" href="logout.php">Вийти</a>
</div>
</body>
</html>
