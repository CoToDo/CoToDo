<?php

namespace App\Repository;

use App\Constants;
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
    /**
     * TeamRepository constructor.
     * @param RegistryInterface $registry
     */
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
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Int Returns number of Team leaders
     */
    public function numberOfLeaders($id)
    {
        $q = $this->createQueryBuilder('t')
            ->join('t.roles', 'r')
            ->andWhere('t.id = :id and r.type = :leader')
            ->setParameter('id', $id)
            ->setParameter('leader', Constants::LEADER)
            ->select('COUNT(t)')
            ->getQuery()
            ->getResult()
            ;

        return isset($q[0][1]) ? $q[0][1] : null;

    }

}
