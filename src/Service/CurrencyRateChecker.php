<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\CurrencyRateClientInterface;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\NonUniqueResultException;

class CurrencyRateChecker
{
    public function __construct(
        private readonly string $threshold,
        private readonly CurrencyRateClientInterface $currencyRateClient,
        private readonly ExchangeRateRepository $exchangeRateRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkExchangeRate(string $currencyPair): CurrencyRateCheckerResponse
    {
        list($currentRateBuy, $currentRateSell) = $this->currencyRateClient->getCurrencyRatesByNumbers($currencyPair);
        list($currencyCodeA, $currencyCodeB) = explode('/', $currencyPair);

        $result = null;
        $prevExchangeRate = $this->exchangeRateRepository->getLastByCurrencyNumbers(
            $currencyCodeA,
            $currencyCodeB,
            $this->currencyRateClient::RATE_KEY
        );

        if ($prevExchangeRate !== null) {
            $previousRateBuy = $prevExchangeRate->getRateBuy();
            $previousRateSell = $prevExchangeRate->getRateSell();

            $result = $this->calculateAndFormatRateDiff(
                $currentRateBuy,
                $previousRateBuy,
                $currentRateSell,
                $previousRateSell
            );
        }

        $this->exchangeRateRepository->add(
            new ExchangeRate(
                $currencyCodeA,
                $currencyCodeB,
                $currentRateBuy,
                $currentRateSell,
                $this->currencyRateClient::RATE_KEY
            )
        );

        return new CurrencyRateCheckerResponse($result);
    }

    private function calculateAndFormatRateDiff(
        float $currentRateBuy,
        float $previousRateBuy,
        float $currentRateSell,
        float $previousRateSell
    ): ?string
    {
        if (abs($currentRateBuy - $previousRateBuy) <= $this->threshold &&
            abs($currentRateSell - $previousRateSell) <= $this->threshold
        )
        {
            return null;
        }

        return $currentRateBuy > $previousRateBuy
            ? "The rate increased by " . abs($currentRateBuy - $previousRateBuy)
            : "The rate decreased by " . abs($currentRateBuy - $previousRateBuy);
    }
}
