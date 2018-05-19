<?php

namespace App\Repository;

use App\Entity\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Work|null find($id, $lockMode = null, $lockVersion = null)
 * @method Work|null findOneBy(array $criteria, array $orderBy = null)
 * @method Work[]    findAll()
 * @method Work[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkRepository extends ServiceEntityRepository
{
    /**
     * WorkRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Work::class);
    }

    /**
     * @return Work[] Return work with not set finish date
     */
    public function findWorkWithoutFinishDate($userId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId)
            ->andWhere('w.endDate IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findUniqueWorks($taskId) {
        return $this->createQueryBuilder('w')
            ->join('w.task', 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $taskId)
            ->andWhere('w.endDate IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findClosedWorks($taskId) {
        return $this->createQueryBuilder('w')
            ->join('w.task', 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $taskId)
            ->andWhere('w.endDate IS NOT NULL')
            ->orderBy('w.endDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

}
