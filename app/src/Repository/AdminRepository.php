<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Admin>
 */
class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Find users with specific roles.
     *
     * @param array $roles
     * @return Admin[]
     */
    public function findByRoles(array $roles): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.roles LIKE :roles')
            ->setParameter('roles', '%'.implode('%', $roles).'%')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Admin[] Returns an array of Admin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Admin
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function searchAdmins(string $query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.firstName LIKE :query')
            ->orWhere('p.lastName LIKE :query')
            ->orWhere('p.localisation LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
