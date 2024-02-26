<?php

declare(strict_types=1);

namespace App\Client;

interface CurrencyRateClientInterface
{
    public const RATE_KEY = 'RATE_KEY';
    public function getCurrencyRatesByNumbers(string $currencyPair);
}