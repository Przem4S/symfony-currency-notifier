<?php

namespace App\Repository;

use App\Entity\Member;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @return Subscription[] Returns an array of Subscription objects
     */
    public function findByMember(Member $member)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.member = :val')
            ->setParameter('val', $member)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Subscription[] Returns an array of Subscription objects
     */
    public function findByMemberAndStatus(Member $member, bool $active)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.member = :val')
            ->andWhere('s.active = :active')
            ->setParameter('val', $member)
            ->setParameter('active', ($active ? 1 : 0))
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
