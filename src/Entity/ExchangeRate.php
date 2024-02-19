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

    #[Column(type: Types::INTEGER, length: 3, nullable: false)]
    private int $currencyCodeA;

    #[Column(type: Types::INTEGER, length: 3, nullable: false)]
    private int $currencyCodeB;

    #[Column(type: Types::DECIMAL, precision: 10, scale: 6)]
    private float $rateBuy;
    #[Column(type: Types::DECIMAL, precision: 10, scale: 6)]
    private float $rateSell;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $recordedAt;

    public function __construct(
        int $currencyCodeA,
        int $currencyCodeB,
        float $rateBuy,
        float $rateSell,
    )
    {
        $this->currencyCodeA = $currencyCodeA;
        $this->currencyCodeB = $currencyCodeB;
        $this->rateBuy = $rateBuy;
        $this->rateSell = $rateSell;

        $this->recordedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrencyCodeA(): int
    {
        return $this->currencyCodeA;
    }

    public function getCurrencyCodeB(): int
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
}
