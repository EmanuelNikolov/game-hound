<?php

namespace App\Repository;


use App\Controller\GameController;
use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findLatest()
    {
        return $this->createQueryBuilder('g')
          ->where('g.cover IS NOT NULL')
          ->orderBy('g.id', 'DESC')
          ->setMaxResults(GameController::PAGE_LIMIT)
          ->getQuery()
          ->getResult()
          ;
    }
}