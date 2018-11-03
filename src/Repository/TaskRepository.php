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
    /**
     * TaskRepository constructor.
     * @param RegistryInterface $registry
     */
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
            ->andWhere('w.endDate IS NULL')
            ->setParameter('val', $id)
            ->orderBy('t.priority, t.deadline', 'ASC')
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

    /**
     * @return Task[] Returns count of closed tasks in project
     */
    public function getCountClosedTasks($projectId) {
        return $this->createQueryBuilder('t')
        ->andWhere('t.project = :id')
            ->setParameter('id', $projectId)
            ->andWhere('t.completionDate IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $projectId
     * @return int
     */
    public function  findTaskInProject($projectId, $taskName) {
        $q = $this->createQueryBuilder('t')
            ->select('t.id')
            ->leftJoin('t.project', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->andWhere('t.name = :name')
            ->setParameter('name', $taskName)
            ->getQuery();
        $res = $q->getResult();
        return isset($res[0]['id']) ? $res[0]['id'] : null;
    }

}
