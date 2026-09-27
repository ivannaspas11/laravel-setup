<?php
session_start();

// Тестові облікові дані
$valid_username = 'admin';$valid_password_hash = password_hash('secret123', PASSWORD_DEFAULT);

$error = '';

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password =$_POST['password'] ?? '';

    if ($username === '' || $password === '') {$error = "Будь ласка, заповніть усі поля.";
    } elseif ($username ===$valid_username && password_verify($password,$valid_password_hash)) {
        // Оновлюємо ідентифікатор сесії
        session_regenerate_id(true);

        // Записуємо дані у сесію
        $_SESSION['user'] = [
            'username'   => $username,
            'logged_in'  => true,
        ];

        // Перенаправлення, щоб уникнути повторного надсилання форми
        header('Location: index.php');
        exit;
    } else {
        $error = "Невірний логін або пароль.";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Робота з $_SESSION</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 380px;
        }
        h2 {
            margin-top: 0;
            color: #1e293b;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
            color: #334155;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.65rem 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
        }
        input:focus {
            outline: 2px solid #2563eb;
            border-color: transparent;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
            margin-top: 1.5rem;
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }
        .alert-error {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 0.65rem 0.8rem;
            border-radius: 6px;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
        }
        .meta-info {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="card">
    <?php if (isset($_SESSION['user']['logged_in']) && $_SESSION['user']['logged_in'] === true): ?>
        <!-- Стан 1: Користувач увійшов -->
        <h2>Привіт, <?= htmlspecialchars($_SESSION['user']['username']); ?>!👋 </h2>
        <p>Ви успішно увійшли в особистий кабінет.</p>
    
        
        <a href="logout.php" class="btn btn-danger">Вийти</a>
    <?php else: ?>
        <!-- Стан 2: Користувач не авторизований -->
        <h2>Вхід у систему</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Логін:</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" name="login_submit" class="btn btn-primary">Увійти</button>
        </form>

        <p class="meta-info" style="margin-top: 1.2rem; text-align: center;">
            Логін: <strong>admin</strong> | Пароль: <strong>secret123</strong>
        </p>
    <?php endif; ?>
</div>

</body>
</html>