<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return Team[] Returns an array of Team objects
     */
    public function findMyTeams($id)
    {
        return $this->createQueryBuilder('t')
            ->join('t.roles', 'r')
            ->andWhere('r.user = :id')
            ->setParameter('id', $id)
            ->orderBy('t.name', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return User[] Returns an array of Team objects
     */
    public function findProjectTeamMembers($id)
    {
        return $this->createQueryBuilder('t')
            ->join('t.roles', 'r')
            ->andWhere('r.user = :id')
            ->setParameter('id', $id)
            ->orderBy('t.name', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
    /*
    public function findOneBySomeField($value): ?Team
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
