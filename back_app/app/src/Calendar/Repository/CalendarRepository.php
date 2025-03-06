<?php

namespace App\Calendar\Repository;

use App\Calendar\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    public function findByYearMonth(int $year, int $month)
    {
        $start = new \DateTime("$year-$month-01");
        $end = (clone $start)->modify('last day of this month');

        return $this->createQueryBuilder('c')
            ->where('c.date BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}