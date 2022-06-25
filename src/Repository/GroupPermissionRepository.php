<?php

namespace App\Repository;

use App\Entity\GroupPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupPermission[]    findAll()
 * @method GroupPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupPermission::class);
    }

    // /**
    //  * @return GroupPermission[] Returns an array of GroupPermission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupPermission
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
