<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;
use mysql_xdevapi\Result;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
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

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByField($sortie,$campus): array
    {
        $requete = $this->createQueryBuilder('s');
        if ($campus != null) {
            $requete->andWhere('s.campus = :campus')->setParameter('campus', $campus);
        }
        if ($sortie != null) {
            $requete->andWhere('s.nom = :nomSortie')->setParameter('nomSortie', $sortie->getNom());
        }
        return $requete
            //->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

    }

    /**
     * @return Sortie[] Returns an array of Sortie objects of non Inscrit
     */
    public function findNonInscrit($user) {
        $requete =  $this->createQueryBuilder('s' );
        return $requete
            ->andWhere(':value MEMBER OF s.participants')
            ->setParameter('value', $user->getId())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects by date
     */
    public function findByDate($beginDate,$endDate) {
        $requete =  $this->createQueryBuilder('s' );
        return $requete
            ->where("s.dateHeureDebut > ?1")
            ->andWhere("s.dateHeureDebut < ?2")
            ->setParameter(1, $beginDate)
            ->setParameter(2, $endDate)
            ->getQuery()
            ->getResult();



    }

//    public function findOneBySomeField($listeNoInscript): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.participant = :val')
//            ->setParameter('val', !$listeNoInscript)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
