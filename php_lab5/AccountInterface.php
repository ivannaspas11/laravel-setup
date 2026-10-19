<?php

declare(strict_types=1);


 // Інтерфейс банківського рахунку.
 
interface AccountInterface
{
    // Поповнення рахунку
    public function deposit(float $amount): void;

    // Зняття коштів з рахунку. 
    public function withdraw(float $amount): void;

    // Отримання поточного балансу
    public function getBalance(): float;
}