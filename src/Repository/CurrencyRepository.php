<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * Get currency by ISO code
     *
     * @return Currency|null
     */
    public function findCurrencyByISO(string $iso)
    {
        $result = $this->createQueryBuilder('c')
            ->andWhere('c.iso = :val')
            ->setParameter('val', $iso)
            ->getQuery()->getResult();

        if(is_array($result)) {
            return array_shift($result);
        }

        return null;
    }
}
