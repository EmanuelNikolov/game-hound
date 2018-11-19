<?php

namespace App\Repository;

use App\Entity\GameCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GameCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameCollection[]    findAll()
 * @method GameCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameCollectionRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GameCollection::class);
    }

    public static function createPaginatedGamesCriteria(
      int $offset,
      int $limit
    ): Criteria {
        return Criteria::create()
          ->setFirstResult($offset)
          ->setMaxResults($limit);
    }

    //    /**
    //     * @return GameCollection[] Returns an array of GameCollection objects
    //     */
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
    public function findOneBySomeField($value): ?GameCollection
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
