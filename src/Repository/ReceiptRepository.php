<?php

namespace App\Repository;

use App\Entity\Receipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Receipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Receipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Receipt[]    findAll()
 * @method Receipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Receipt::class);
    }

    public function all($user)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->andWhere('s.buyer = :id')
            ->setParameter('id', $user)
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function jobs($worker)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->andWhere('s.worker = :id')
            ->setParameter('id', $worker)
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function firmJobs($firm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->innerJoin('s.worker', 'w')
            ->andWhere('w.firmName = :firmname')
            ->setParameter('firmname', $firm)
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function allJobs()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
