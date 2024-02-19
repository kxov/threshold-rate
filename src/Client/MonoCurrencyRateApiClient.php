<?php

declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

class MonoCurrencyRateApiClient implements CurrencyRateClientInterface
{
    private const URI = '/bank/currency';

    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    /**
     * @return array<string, string>
     * @throws CurrencyRateApiClientException
     */
    public function getCurrencyRatesByNumbers(int $currencyCodeA, int $currencyCodeB): array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::URI
            );
        } catch (GuzzleException $exception) {
            throw new CurrencyRateApiClientException($exception->getMessage());
        }

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new CurrencyRateApiClientException('No response from Client');
        }

        $responseData = json_decode((string) $response->getBody(), true);

        if (! is_array($responseData) && JSON_ERROR_NONE !== json_last_error()) {
            throw new CurrencyRateApiClientException(json_last_error_msg());
        }

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
}
