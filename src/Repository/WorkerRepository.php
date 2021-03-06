<?php

namespace App\Repository;

use App\Entity\Worker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Worker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Worker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Worker[]    findAll()
 * @method Worker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Worker::class);
    }

    /**
     * @param string $firm
     *
     * @return array
     */
    public function findByFirm($firm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('w')
            ->from($this->_entityName, 'w')
            ->where('w.firmName = :firm')
            ->setParameter('firm', $firm);

        return $qb->getQuery()->getResult();
    }
}
