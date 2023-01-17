<?php

namespace App\Repository;

use App\Entity\PaiementPartenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaiementPartenaire>
 *
 * @method PaiementPartenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaiementPartenaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaiementPartenaire[]    findAll()
 * @method PaiementPartenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementPartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaiementPartenaire::class);
    }

    public function save(PaiementPartenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaiementPartenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PaiementPartenaire[] Returns an array of PaiementPartenaire objects
     */
    public function findByMotCle($criteres): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.refnotededebit like :valMotCle')
            ->setParameter('valMotCle', '%' . $criteres['motcle'] . '%')
            ->orderBy('p.id', 'DESC');

        if (($criteres['dateA'] != null) and ($criteres['dateB'] != null)) {
            $query = $query
                ->andWhere('p.date BETWEEN :valDateA AND :valDateB')
                ->setParameter('valDateA', $criteres['dateA'])
                ->setParameter('valDateB', $criteres['dateB']);
        }

        if ($criteres['partenaire']) {
            $query = $query
                ->andWhere('p.partenaire = :valPartenaire')
                ->setParameter('valPartenaire', $criteres['partenaire']);
        }

        $query = $query
            ->getQuery()
            ->getResult();

        //dd($query);
        //dd($criteres['police']);

        $resultFinal = [];
        if ($criteres['police']) {
            foreach ($query as $popPartenaire) {
                //dd($popTaxe);
                //dd($criteres['police']);
                //dd($popTaxe->getPolices());
                foreach ($popPartenaire->getPolices() as $police) {
                    //dd($police);
                    //dd($criteres['police']->getReference());
                    if ($police->getReference() == $criteres['police']->getReference()) {
                        $resultFinal[] = $popPartenaire;
                    }
                }
            }
        } else {
            $resultFinal = $query;
        }

        //return $query;
        return $resultFinal;
    }

    //    public function findOneBySomeField($value): ?PaiementPartenaire
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
