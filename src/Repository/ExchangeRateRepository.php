<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function add(ExchangeRate $exchangeRate): void
    {
        $this->_em->persist($exchangeRate);
        $this->_em->flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getLastByCurrencyNumbers(int $currencyCodeA, int $currencyCodeB): ?ExchangeRate
    {
        return $this->createQueryBuilder('er')
            ->select('er')
            ->where('er.currencyCodeA = :cCodeA')
            ->andWhere('er.currencyCodeB = :cCodeB')
            ->setParameter('cCodeA', $currencyCodeA)
            ->setParameter('cCodeB', $currencyCodeB)
            ->orderBy('er.recordedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
