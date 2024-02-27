<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\CurrencyRateClientInterface;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class CurrencyRateChecker
{
    public function __construct(
        private readonly string $threshold,
        #[TaggedIterator('app.currency.client')]
        private readonly iterable $clients,
        private readonly ExchangeRateRepository $exchangeRateRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkExchangeRate(string $currencyPair): CurrencyRateCheckerResponse
    {
        $result = null;
        /** @var CurrencyRateClientInterface $client */
        foreach ($this->clients as $client) {
            list($currentRateBuy, $currentRateSell) = $client->getCurrencyRatesByNumbers($currencyPair);
            list($currencyCodeA, $currencyCodeB) = explode('/', $currencyPair);

            $prevExchangeRate = $this->exchangeRateRepository->getLastByCurrencyNumbers(
                $currencyCodeA,
                $currencyCodeB,
                $client::RATE_KEY
            );

            if ($prevExchangeRate !== null) {
                $previousRateBuy = $prevExchangeRate->getRateBuy();
                $previousRateSell = $prevExchangeRate->getRateSell();

                $result[] = $this->calculateAndFormatRateDiff(
                    $currentRateBuy,
                    $previousRateBuy,
                    $currentRateSell,
                    $previousRateSell,
                    $client::RATE_KEY
                );
            }

            $this->exchangeRateRepository->add(
                new ExchangeRate(
                    $currencyCodeA,
                    $currencyCodeB,
                    $currentRateBuy,
                    $currentRateSell,
                    $client::RATE_KEY
                )
            );
        }

        return new CurrencyRateCheckerResponse($result);
    }

    private function calculateAndFormatRateDiff(
        float $currentRateBuy,
        float $previousRateBuy,
        float $currentRateSell,
        float $previousRateSell,
        string $providerKey,
    ): ?string
    {
        if (abs($currentRateBuy - $previousRateBuy) <= $this->threshold &&
            abs($currentRateSell - $previousRateSell) <= $this->threshold
        )
        {
            return null;
        }

        return $providerKey .'# '. ($currentRateBuy > $previousRateBuy
            ? "The rate increased by " . abs($currentRateBuy - $previousRateBuy)
            : "The rate decreased by " . abs($currentRateBuy - $previousRateBuy)) . ' ';
    }
}
