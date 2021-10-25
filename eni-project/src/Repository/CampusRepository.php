<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

     /**
      * @return Campus[] Returns an array of Campus objects
      */

    public function findByName($name): array
    {
        $qb = $this->createQueryBuilder('s');

        $words = explode(" ",$name);
        foreach ($words as $key => $word) {
            $qb->andWhere("s.nom LIKE :name")
                ->setParameter("name", '%'.$word.'%');
        };

        $query = $qb->getQuery();
        return $query->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Campus
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
