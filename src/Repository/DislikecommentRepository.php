<?php

namespace App\Repository;

use App\Entity\Dislikecomment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dislikecomment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dislikecomment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dislikecomment[]    findAll()
 * @method Dislikecomment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DislikecommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dislikecomment::class);
    }

    // /**
    //  * @return Dislikecomment[] Returns an array of Dislikecomment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dislikecomment
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
