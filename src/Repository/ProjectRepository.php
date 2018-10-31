<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    /**
     * ProjectRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return Project[] Returns an array of Project objects
     */
    public function findMyProjects($id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.team', 't')
            ->join('t.roles', 'r')
            ->andWhere('r.user = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findAllSearch()
    {
        return $this->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()
            ->getResult();
    }


    public function findProjectsMatch($param)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :param')
            ->setParameter('param', '%' . $param . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $name
     * @param User $user
     * @return int
     */
    public function findIdProjectByName(string $name, User $user)
    {
       /** select p.id from project p join team t on(t.id=p.team_id) join role r on (r.team_id=t.id) join app_users u on (r.user_id = u.id) where p.name = 'new_project'; */
       /** SELECT p.id FROM project p LEFT JOIN team t LEFT JOIN role r LEFT JOIN app_users u WHERE t.id = p.team_id AND t.id = r.team_id AND u.id = r.user_id AND u.id = '4' AND p.name = 'new_project'; */
         $q = $this->createQueryBuilder('p')
             ->select('p.id')
            ->leftJoin('p.team', 't')
            ->leftJoin('t.roles', 'r')
            ->leftJoin('r.user', 'u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $user->getId())
            ->andWhere('p.name = :param')
            ->setParameter("param", $name)
            ->getQuery();
        $res = $q->getResult();
         return isset($res[0]['id']) ? $res[0]['id'] : null;
    }

}
