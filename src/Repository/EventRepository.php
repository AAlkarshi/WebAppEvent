<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findFromDate(\DateTimeImmutable $date): array {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateTime_event >= :date')
            ->setParameter('date', $date)
            ->orderBy('e.dateTime_event', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBetweenDates(\DateTimeImmutable $start, \DateTimeImmutable $end): array {
    return $this->createQueryBuilder('e')
        ->andWhere('e.dateTime_event BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('e.dateTime_event', 'ASC')
        ->getQuery()
        ->getResult();
}



//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
