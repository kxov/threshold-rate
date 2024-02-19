<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\CurrencyRateClientInterface;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\NonUniqueResultException;

class CurrencyRateChecker
{
    private const CODE_TO_NUMBER_MAP = [
        'UAH' => 980,
        'USD' => 840,
        'EUR' => 978,
    ];

    public function __construct(
        private readonly string $threshold,
        private readonly CurrencyRateClientInterface $currencyRateClient,
        private readonly ExchangeRateRepository $exchangeRateRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws CurrencyRateCheckerException
     */
    public function checkExchangeRate(string $currencyPair): CurrencyRateCheckerResponse
    {
        list($currencyCodeA, $currencyCodeB) = $this->getCurrencyNumbers($currencyPair);
        list($currentRateBuy, $currentRateSell) = $this->currencyRateClient->getCurrencyRatesByNumbers($currencyCodeA, $currencyCodeB);

        $result = null;
        $prevExchangeRate = $this->exchangeRateRepository->getLastByCurrencyNumbers($currencyCodeA, $currencyCodeB);
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

        $this->exchangeRateRepository->add(new ExchangeRate($currencyCodeA, $currencyCodeB, $currentRateBuy, $currentRateSell));

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
