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
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.buyer = :id')
            ->setParameter('id', $user)
            ->andWhere('s.activity = 0')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function allByOffice($user, $value)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->innerJoin('s.worker', 'w')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('w.firmName like :query')
            ->setParameter('query', "%" . $value . "%")
            ->andWhere('s.buyer = :id')
            ->setParameter('id', $user)
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findInTwoWeeks($worker, $twoWeeks)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService BETWEEN CURRENT_DATE() and :date')
            ->andWhere('s.worker = :id')
            ->setParameter('id', $worker)
            ->setParameter('date', $twoWeeks)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function jobs($worker)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.worker = :id')
            ->setParameter('id', $worker)
            ->andWhere('s.activity = 0')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function firmJobs($firm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->innerJoin('s.worker', 'w')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('w.firmName = :firmname')
            ->setParameter('firmname', $firm)
            ->andWhere('s.activity = 0')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function allJobs()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.activity = 0')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function incomingJobs($worker)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService > CURRENT_TIMESTAMP()')
            ->andWhere('s.worker = :id')
            ->setParameter('id', $worker)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function firmIncomingJobs($firm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->innerJoin('s.worker', 'w')
            ->where('s.startOfService > CURRENT_TIMESTAMP()')
            ->andWhere('w.firmName = :firmname')
            ->setParameter('firmname', $firm)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function allIncomingJobs()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService > CURRENT_TIMESTAMP()')
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function incoming($user)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService > CURRENT_TIMESTAMP()')
            ->andWhere('s.buyer = :id')
            ->setParameter('id', $user)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function missed($user)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.buyer = :id')
            ->setParameter('id', $user)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function missedJobs($worker)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.worker = :id')
            ->setParameter('id', $worker)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function missedFirmJobs($firm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->innerJoin('s.worker', 'w')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('w.firmName = :firmname')
            ->setParameter('firmname', $firm)
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function allMissedJobs()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.startOfService < CURRENT_TIMESTAMP()')
            ->andWhere('s.activity = 1')
            ->orderBy('s.startOfService', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
