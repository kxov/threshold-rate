<?php

declare(strict_types=1);

namespace App\Tests\Functional\Fixtures;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ExchangeRateFixture extends Fixture implements FixtureInterface
{
    public const REFERENCE = 'exchangeRate';

    public function load(ObjectManager $manager): void
    {
        $exchangeRate = new ExchangeRate(
            840,
            980,
            32.2,
            38.2,
        );
        $manager->persist($exchangeRate);

        $this->setReference(self::REFERENCE, $exchangeRate);

        $manager->flush();
    }
}
