<?php


namespace App\Providers;


interface CurrencyProviderInterface
{
    public function getCurrency(string $iso);
    public function getCurrencies();
}