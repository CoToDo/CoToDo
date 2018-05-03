<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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
            ->getResult()
        ;
    }


    public function findAllSearch()
    {
        return $this->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findProjectsMatch($param)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :param')
            ->setParameter('param', '%' . $param . '%' )
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
