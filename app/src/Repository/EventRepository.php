<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Partner;
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

    // Find events by owner entity
    public function findByOwner(Partner $owner): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }

    public function getAll(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.serviceName, e.description, e.startDate, e.endDate, e.price, e.available, e.imageFilename')
            ->getQuery()
            ->getArrayResult();
    }

    public function getDetailsById(int $id): ?array
    {
        return $this->createQueryBuilder('e')
            ->select('e.id, e.serviceName, e.description, e.startDate, e.endDate, e.price, e.available, e.imageFilename')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();;
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
