<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByActivity(int $activityId, int $postId): array
    {
        // On crée un QueryBuilder à partir de l'entité Comment, aliasée 'c'
        return $this->createQueryBuilder('c')
            ->join('c.post', 'p') // Jointure avec la table des posts
            ->join('p.activity', 'a') // Jointure avec la table des activités
            ->andWhere('a.id = :activityId') // Filtre par l'ID de l'activité
            ->andWhere('p.id = :postId') // Filtre également par l'ID du post
            ->setParameter('activityId', $activityId)
            ->setParameter('postId', $postId) // Ajout du paramètre pour le post
            ->orderBy('c.createdAt', 'DESC') // Trie les commentaires par date
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Comment[] Returns an array of Comment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Comment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
