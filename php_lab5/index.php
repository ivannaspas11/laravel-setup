<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

require_once __DIR__ . '/AccountInterface.php';
require_once __DIR__ . '/BankAccount.php';
require_once __DIR__ . '/SavingsAccount.php';


 // Клієнтський код: тестування банківських рахунків.
 

function show(string $text): void
{
    echo $text . PHP_EOL;
}


 // Виконує операцію та виводить повідомлення про помилку у разі винятку
 
function tryOperation(string $title, callable $operation): void
{
    try {
        $operation();
        show("[OK]      $title");
    } catch (Exception $e) {
        show("[ПОМИЛКА] $title -> " . $e->getMessage());
    }
}

show('=== BankAccount ===');
$account = new BankAccount(100, 'USD');
show('Початковий стан: ' . $account);

tryOperation('Поповнення на 50',        fn() => $account->deposit(50));
show('Баланс: ' . $account->getBalance() . ' ' . $account->getCurrency());

tryOperation('Зняття 30',               fn() => $account->withdraw(30));
show('Баланс: ' . $account->getBalance() . ' ' . $account->getCurrency());

tryOperation('Зняття 500 (забагато)',   fn() => $account->withdraw(500));
tryOperation('Поповнення на -20',       fn() => $account->deposit(-20));
tryOperation('Зняття -10',              fn() => $account->withdraw(-10));
tryOperation('Створення рахунку з балансом -5', fn() => new BankAccount(-5, 'EUR'));

show('Підсумок: ' . $account);
show('');

show('=== SavingsAccount ===');
$savings = new SavingsAccount(1000, 'EUR');
show('Початковий стан: ' . $savings);
show('Ставка: ' . SavingsAccount::$interestRate . '%');

tryOperation('Поповнення на 200',       fn() => $savings->deposit(200));
tryOperation('Зняття 100',              fn() => $savings->withdraw(100));
tryOperation('Нарахування відсотків',   fn() => $savings->applyInterest());
show('Після відсотків: ' . $savings);

// Зміна статичної ставки
SavingsAccount::$interestRate = 10.0;
show('Нова ставка: ' . SavingsAccount::$interestRate . '%');
tryOperation('Нарахування відсотків (10%)', fn() => $savings->applyInterest());
show('Після відсотків: ' . $savings);

tryOperation('Зняття 5000 (забагато)',  fn() => $savings->withdraw(5000));
show('');

show('=== Поліморфізм (AccountInterface) ===');
/** @var AccountInterface[] $accounts */
$accounts = [new BankAccount(50, 'USD'), new SavingsAccount(200, 'UAH')];

foreach ($accounts as $acc) {
    tryOperation(get_class($acc) . ': зняття 70', fn() => $acc->withdraw(70));
    show('  Баланс: ' . $acc->getBalance());
}

show('');
show('MIN_BALANCE = ' . BankAccount::MIN_BALANCE);