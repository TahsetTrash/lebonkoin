<?php

namespace App\Repository;

use App\Entity\Download;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Download|null find($id, $lockMode = null, $lockVersion = null)
 * @method Download|null findOneBy(array $criteria, array $orderBy = null)
 * @method Download[]    findAll()
 * @method Download[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Download::class);
    }


    public function findDownloadByUserAndPost($user_id,$post_id) {
        return $this->createQueryBuilder('a')
            ->where('a.user_id = :u_id')
            ->andWhere('a.post_id = :p_id')
            ->setParameter('u_id', $user_id)
            ->setParameter('p_id', $post_id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDownloadByPost($post_id) {
        return $this->createQueryBuilder('a')
            ->where('a.post_id = :p_id')
            ->setParameter('p_id', $post_id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Download[] Returns an array of Download objects
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
    public function findOneBySomeField($value): ?Download
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
