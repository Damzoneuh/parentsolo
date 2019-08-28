<?php

namespace App\Repository;

use App\Entity\ChildWanted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ChildWanted|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChildWanted|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChildWanted[]    findAll()
 * @method ChildWanted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildWantedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChildWanted::class);
    }

    // /**
    //  * @return ChildWanted[] Returns an array of ChildWanted objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChildWanted
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
