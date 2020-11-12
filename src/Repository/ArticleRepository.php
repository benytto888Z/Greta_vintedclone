<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findArticlesByPriceLess($prix)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.prix < :val')
            ->setParameter('val', $prix)
            //->orderBy('a.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article[]
     */
    public function findArticlesByPriceGreat($prix): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Article a
            WHERE a.prix > :prix
            ORDER BY a.prix ASC'
        )->setParameter('prix', $prix);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function filterArticleBy($mot): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT * FROM Article a
        WHERE a.titre like :mot
        ORDER BY a.titre ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['mot' => '%'.$mot.'%']);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }


    public function findUserAndArticle($email): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT u.email vendeur,a.titre nom_article ,a.prix prix_article,a.created_at date_mise_en_ligne
        FROM User u
        INNER JOIN Article a
        ON a.user_id = u.id
        WHERE u.email = :email
        ORDER BY prix DESC
        LIMIT 0, 5
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }







   

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
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
