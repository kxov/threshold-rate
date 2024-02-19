<?php

declare(strict_types=1);

namespace App\Client;

interface CurrencyRateClientInterface
{
    public function getCurrencyRatesByNumbers(int $currencyCodeA, int $currencyCodeB);
}