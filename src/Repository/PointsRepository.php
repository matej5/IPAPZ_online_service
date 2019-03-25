<?php

namespace App\Repository;

use App\Entity\Points;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Points|null find($id, $lockMode = null, $lockVersion = null)
 * @method Points|null findOneBy(array $criteria, array $orderBy = null)
 * @method Points[]    findAll()
 * @method Points[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Points::class);
    }
}
