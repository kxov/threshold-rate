<?php

declare(strict_types=1);

namespace App\Client\Provider;

use App\Client\CurrencyRateApiClientException;
use App\Client\CurrencyRateClientInterface;
use App\Client\RequestClient;

class PrivatCurrencyRateApiClient implements CurrencyRateClientInterface
{
    public const RATE_KEY = 'PRIVAT';

    public function __construct(
        private readonly RequestClient $client,
    ) {
    }

    /**
     * @return array<string, string>
     * @throws CurrencyRateApiClientException
     */
    public function getCurrencyRatesByNumbers(string $currencyPair): array
    {
        $responseData = $this->client->request('GET', 'https://api.privatbank.ua/p24api/pubinfo');

        list($currencyCodeA, $currencyCodeB) = explode('/', $currencyPair);

        $currentRateBuy = null;
        $currentRateSell = null;
        foreach ($responseData as $currency) {
            if ($currency['ccy'] == $currencyCodeA && $currency['base_ccy'] == $currencyCodeB) {
                $currentRateBuy = (float)$currency['buy'];
                $currentRateSell = (float)$currency['sale'];
            }
        }

        return [$currentRateBuy, $currentRateSell];
    }
}