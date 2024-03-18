<?php

declare(strict_types=1);

namespace App\Tests\Functional\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use kxov\ThresholdRateBundle\Entity\ExchangeRate;

final class ExchangeRateFixture extends Fixture implements FixtureInterface
{
    public const REFERENCE = 'exchangeRate';

    public function load(ObjectManager $manager): void
    {
        $exchangeRate = new ExchangeRate(
            'USD',
            'UAH',
            32.2,
            38.2,
            'MONO'
        );
        $manager->persist($exchangeRate);

        $this->setReference(self::REFERENCE, $exchangeRate);

        $manager->flush();
    }
}
