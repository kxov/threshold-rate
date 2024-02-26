<?php

declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

class RequestClient
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    /**
     * @throws CurrencyRateApiClientException
     */
    public function request(string $method, string $url): array
    {
        try {
            $response = $this->client->request(
                $method,
                $url
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

        return $responseData;
    }
}