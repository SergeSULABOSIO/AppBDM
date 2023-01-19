<?php

namespace App\Repository;

use App\Entity\Police;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Police>
 *
 * @method Police|null find($id, $lockMode = null, $lockVersion = null)
 * @method Police|null findOneBy(array $criteria, array $orderBy = null)
 * @method Police[]    findAll()
 * @method Police[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoliceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Police::class);
    }

    public function save(Police $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Police $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Police[] Returns an array of Police objects
     */
    public function findByMotCle($criteres): array
    {

        $query = $this->createQueryBuilder('p')
            ->where('p.reference like :valMotCle')
            ->orWhere('p.remarques like :valMotCle')
            ->orWhere('p.reassureurs like :valMotCle')
            ->setParameter('valMotCle', '%' . $criteres['motcle'] . '%')
            ->orderBy('p.dateeffet', 'DESC');

        if (($criteres['dateA'] != null) and ($criteres['dateB'] != null)) {
            $query = $query
                ->andWhere('p.dateeffet BETWEEN :valDateA AND :valDateB')
                ->setParameter('valDateA', $criteres['dateA'])
                ->setParameter('valDateB', $criteres['dateB']);
        }

        $query = $query
            ->getQuery()
            ->getResult();

        //dd($query);
        //dd($criteres['police']);


        //FILTRE POUR PRODUIT
        $resultProduit = [];
        if ($criteres['produit']) {
            foreach ($query as $police) {
                if ($police->getProduit()->getId() == $criteres['produit']->getId()) {
                    $resultProduit[] = $police;
                }
            }
        } else {
            $resultProduit = $query;
        }


        //FILTRE POUR CLIENT
        $resultClient = [];
        if ($criteres['client']) {
            foreach ($resultProduit as $police) {
                if ($police->getClient()->getId() == $criteres['client']->getId()) {
                    $resultClient[] = $police;
                }
            }
        } else {
            $resultClient = $resultProduit;
        }


        //FILTRE POUR PARTENAIRE
        $resultPartenaire = [];
        if ($criteres['partenaire']) {
            foreach ($resultClient as $police) {
                if ($police->getPartenaire()->getId() == $criteres['partenaire']->getId()) {
                    $resultPartenaire[] = $police;
                }
            }
        } else {
            $resultPartenaire = $resultClient;
        }


        //FILTRE POUR PARTENAIRE
        $resultAssureur = [];
        if ($criteres['assureur']) {
            foreach ($resultPartenaire as $police) {
                foreach ($police->getAssureurs() as $assureur) {
                    if ($assureur->getId() == $criteres['assureur']->getId()) {
                        $resultAssureur[] = $police;
                    }
                }
            }
        } else {
            $resultAssureur = $resultPartenaire;
        }

        $resultFinal = $resultAssureur;
        //return $query;
        return $resultFinal;
    }

    //    public function findOneBySomeField($value): ?Police
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
