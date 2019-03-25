<?php

namespace App\Repository;

use App\Entity\LikeDislike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LikeDislike|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeDislike|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeDislike[]    findAll()
 * @method LikeDislike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeDislikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LikeDislike::class);
    }
}
