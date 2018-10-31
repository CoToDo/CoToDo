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

    public function findAsigneeId($userId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId)
            ->andWhere('w.startDate IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findUserTimes($taskId) {
        $sql = 'select u.mail, sum from (select user_id, SUM(strftime(\'%s\', w.end_date) - strftime(\'%s\', start_date)) sum from work w where w.task_id = :id group by user_id) join app_users u on (user_id=u.id)';
        $params = array('id' => $taskId);

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();
    }
}