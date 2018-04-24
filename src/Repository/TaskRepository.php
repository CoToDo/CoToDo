<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findMyTasksSortedByPriority($id)
    {
        return $this->createQueryBuilder('t')
            ->join('t.works',"w")
            ->where('w.user = :val')
            ->andWhere('t.completionDate IS NULL')
            ->setParameter('val', $id)
            ->orderBy('t.priority, t.deadline', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findMyTasksSortedByPriorityMatch($id, $param)
    {
        return $this->createQueryBuilder('t')
            ->join('t.works',"w")
            ->where('w.user = :val')
            ->setParameter('val', $id)
            ->andWhere('t.name LIKE :param')
            ->setParameter('param', '%' . $param . '%' )
            ->orderBy('t.priority, t.deadline', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findTasksSortedByCompletionDate($id)
    {
        return $this->createQueryBuilder('t')
            ->join('t.project',"p")
            ->where('p.id = :val')
            ->setParameter('val', $id)
            ->orderBy('t.completionDate, t.priority, t.deadline', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findUncompleteTasks($id)
    {
        return $this->createQueryBuilder('t')
            ->join('t.project',"p")
            ->where('p.id = :val')
            ->andWhere('t.completionDate IS NULL')
            ->setParameter('val', $id)
            ->orderBy('t.priority, t.deadline', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
