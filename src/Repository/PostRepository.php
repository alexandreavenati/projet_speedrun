<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findByActivityOrderByDurationAsc(int $activityId): array
    {
        return $this->createQueryBuilder('p') // Crée un QueryBuilder pour l'entité aliasée par 'p'
            ->andWhere('p.activity = :activityId') // Ajoute une condition WHERE : sélectionne uniquement les enregistrements où l'activité correspond à l'identifiant donné
            ->setParameter('activityId', $activityId) // Lie la variable PHP $activityId au paramètre nommé :activityId pour éviter l'injection SQL
            ->orderBy('p.video_duration', 'ASC') // Trie les résultats par durée de vidéo (video_duration) de manière croissante (ASC)
            ->getQuery() // Génère la requête Doctrine à partir du QueryBuilder
            ->getResult(); // Exécute la requête et retourne le résultat sous forme de tableau d'objets (souvent des entités)
    }

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
