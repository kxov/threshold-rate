<?php

declare(strict_types=1);

namespace App\Client\Provider;

use App\Client\CurrencyRateApiClientException;
use App\Client\CurrencyRateClientInterface;
use App\Client\RequestClient;
use App\Service\CurrencyRateCheckerException;

class MonoCurrencyRateApiClient implements CurrencyRateClientInterface
{
    public const CODE_TO_NUMBER_MAP = [
        'UAH' => 980,
        'USD' => 840,
        'EUR' => 978,
    ];

    public const RATE_KEY = 'MONO';

    public function __construct(
        private readonly RequestClient $client,
    ) {
    }

    /**
     * @return array<string, string>
     * @throws CurrencyRateApiClientException
     * @throws CurrencyRateCheckerException
     */
    public function getCurrencyRatesByNumbers(string $currencyPair): array
    {
        $responseData = $this->client->request('GET', 'https://api.monobank.ua/bank/currency');

        list($currencyCodeA, $currencyCodeB) = $this->getCurrencyNumbers($currencyPair);

        $currentRateBuy = null;
        $currentRateSell = null;
        foreach ($responseData as $currency) {
            if ($currency['currencyCodeA'] == $currencyCodeA && $currency['currencyCodeB'] == $currencyCodeB) {
                $currentRateBuy = $currency['rateBuy'];
                $currentRateSell = $currency['rateSell'];
            }
        }

        return [$currentRateBuy, $currentRateSell];
    }

    /**
     * @throws CurrencyRateCheckerException
     */
    private function getCurrencyNumbers(string $currencyPair): array
    {
        $currencyCodes = explode('/', $currencyPair);
        $currencyCodeA = self::CODE_TO_NUMBER_MAP[$currencyCodes[0]] ?? null;
        $currencyCodeB = self::CODE_TO_NUMBER_MAP[$currencyCodes[1]] ?? null;

        if ($currencyCodeA === null || $currencyCodeB === null) {
            throw new CurrencyRateCheckerException('Invalid currency pair');
        }

        return [$currencyCodeA, $currencyCodeB];
    }
}
