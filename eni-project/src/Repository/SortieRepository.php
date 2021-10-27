<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
    * Retourne une liste de sorties, filtrée selon les paramétres fournis
    */
    public function findWithFilters($site,$words,$organisateur,$inscrit,$non_inscrit,$passees,$start,$end,$user){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')->from('App\Entity\Sortie', 's');

        $site ? $qb->andWhere('s.campus = :site')->setParameter('site',$site) : null;
        $organisateur ? $qb->andWhere('s.organisateur = :user')->setParameter('user', $user) : null;
        $inscrit ? $qb->andWhere(':user MEMBER OF s.participant')->setParameter('user', $user) : null;
        $non_inscrit ? $qb->andWhere(':user NOT MEMBER OF s.participant')->setParameter('user',$user) : null;
        $passees ? $qb->andWhere('s.dateHeureDebut < :now')->setParameter('now',new \DateTime('now')) : null;
        
        $words = explode(" ",$words);
        foreach ($words as $key => $word) {
            if (empty($word)) { continue; }
            $qb->andWhere("s.nom LIKE :m{$key}")
            ->setParameter("m{$key}", '%'.$word.'%');
        };

        if($start && $end){
            $qb->andWhere('s.dateHeureDebut >  :start')
            ->andWhere('s.dateHeureDebut <  :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }
}
