<?php

declare(strict_types=1);

require_once __DIR__ . '/AccountInterface.php';


 // Базовий банківський рахунок
 
class BankAccount implements AccountInterface
{
    // Мінімальний допустимий баланс рахунку
    public const MIN_BALANCE = 0;

    protected float $balance;
    protected string $currency;

    
     // @throws Exception якщо початковий баланс менший за MIN_BALANCE
     
    public function __construct(float $balance = 0, string $currency = 'USD')
    {
        if ($balance < static::MIN_BALANCE) {
            throw new Exception(
                'Початковий баланс не може бути меншим за ' . static::MIN_BALANCE
            );
        }

        $this->balance  = $balance;
        $this->currency = $currency;
    }

    
     // @throws Exception якщо сума некоректна (не додатна)
     
    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Сума поповнення має бути додатною');
        }

        $this->balance += $amount;
    }

    
     // @throws Exception якщо сума некоректна або недостатньо коштів
     
    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Сума зняття має бути додатною');
        }

        if ($this->balance - $amount < static::MIN_BALANCE) {
            throw new Exception('Недостатньо коштів');
        }

        $this->balance -= $amount;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return sprintf('%s: %.2f %s', static::class, $this->balance, $this->currency);
    }
}