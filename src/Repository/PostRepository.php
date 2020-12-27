<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */

    public function findPostsByOwnerId($ownerId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.ownerId = :val')
            ->setParameter('val', $ownerId)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPostsByField($field, $category)
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


    
    public function findPostById($id): ?Post
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
