<?php

declare(strict_types=1);

namespace App\Tests\Tools;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

trait FixtureTools
{
    public function getDatabaseTools(): AbstractDatabaseTool
    {
        return static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function loadFixtures(string $fixture, $append = false): object
    {
        $executor = $this->getDatabaseTools()->loadFixtures([$fixture], $append);

        return $executor->getReferenceRepository()->getReference($fixture::REFERENCE);
    }
}
