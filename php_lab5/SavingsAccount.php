<?php

declare(strict_types=1);

require_once __DIR__ . '/BankAccount.php';


 // Накопичувальний рахунок із нарахуванням відсотків.
 
class SavingsAccount extends BankAccount
{
    // Відсоткова ставка (у відсотках), спільна для всіх накопичувальних рахунків. 
    public static float $interestRate = 5.0;

    
     // Нараховує відсотки на поточний баланс.
     
    public function applyInterest(): void
    {
        $interest = $this->balance * static::$interestRate / 100;
        $this->balance += $interest;
    }
}