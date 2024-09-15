<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Get all reservations
     * 
     * @return Reservation[]
     */
    public function getAll(): array
    {
        return $this->findAll();
    }

     /**
     * Find reservations by event.
     *
     * @param int $eventId
     * @return Reservation[] Returns an array of Reservation objects
     */
    public function findByEvent(int $eventId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.event = :event')
            ->setParameter('event', $eventId)
            ->orderBy('r.reservationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations by product.
     *
     * @param int $productId
     * @return Reservation[] Returns an array of Reservation objects
     */
    public function findByProduct(int $productId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.product = :product')
            ->setParameter('product', $productId)
            ->orderBy('r.reservationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a reservation by ID.
     *
     * @param int $id
     * @return Reservation|null Returns a Reservation object or null
     */
    public function findById(int $id): ?Reservation
    {
        return $this->find($id);
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
