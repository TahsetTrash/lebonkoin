<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    // /**
    //  * @return Ad[] Returns an array of Ad objects
    //  */

    public function findAdsByOwnerId($ownerId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.ownerId = :val')
            ->setParameter('val', $ownerId)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAdsByField($field, $category)
    {
        $query = $this->createQueryBuilder('a');
        return $query
            //->where('a.ownerId = :val')
            ->andwhere('a.name LIKE :val')
            ->andWhere('a.category = :category')
            ->orWhere('a.description LIKE :val')
            ->setParameter('val', '%' . $field . '%')
            ->setParameter('category', $category)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
