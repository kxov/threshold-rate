<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::STRING, length: 3, nullable: false)]
    private string $currencyCodeA;

    #[Column(type: Types::STRING, length: 3, nullable: false)]
    private string $currencyCodeB;

    #[Column(type: Types::DECIMAL, precision: 10, scale: 6)]
    private float $rateBuy;
    #[Column(type: Types::DECIMAL, precision: 10, scale: 6)]
    private float $rateSell;

    #[Column(type: Types::STRING, length: 10)]
    private string $rateKey;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $recordedAt;

    public function __construct(
        string $currencyCodeA,
        string $currencyCodeB,
        float $rateBuy,
        float $rateSell,
        string $rateKey,
    )
    {
        $this->currencyCodeA = $currencyCodeA;
        $this->currencyCodeB = $currencyCodeB;
        $this->rateBuy = $rateBuy;
        $this->rateSell = $rateSell;
        $this->rateKey = $rateKey;

        $this->recordedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrencyCodeA(): string
    {
        return $this->currencyCodeA;
    }

    public function getCurrencyCodeB(): string
    {
        return $this->currencyCodeB;
    }

    public function getRateBuy(): float
    {
        return $this->rateBuy;
    }

    public function getRateSell(): float
    {
        return $this->rateSell;
    }

    public function getRecordedAt(): \DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function getRateKey(): string
    {
        return $this->rateKey;
    }
}
