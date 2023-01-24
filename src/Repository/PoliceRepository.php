<?php

namespace App\Repository;

use App\Entity\Police;
use App\Agregats\PoliceAgregat;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function findByMotCle($criteres, $agregat): array
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
                if ($police->getProduit()) {
                    if ($police->getProduit()->getId() == $criteres['produit']->getId()) {
                        $resultProduit[] = $police;
                    }
                }
            }
        } else {
            $resultProduit = $query;
        }


        //FILTRE POUR CLIENT
        $resultClient = [];
        if ($criteres['client']) {
            foreach ($resultProduit as $police) {
                if ($police->getClient()) {
                    if ($police->getClient()->getId() == $criteres['client']->getId()) {
                        $resultClient[] = $police;
                    }
                }
            }
        } else {
            $resultClient = $resultProduit;
        }


        //FILTRE POUR PARTENAIRE
        $resultPartenaire = [];
        if ($criteres['partenaire']) {
            foreach ($resultClient as $police) {
                if ($police->getPartenaire()) {
                    if ($police->getPartenaire()->getId() == $criteres['partenaire']->getId()) {
                        $resultPartenaire[] = $police;
                    }
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


        //chargement des données sur l'agregat
        if ($agregat !== null) {
            $primetotale = 0;
            $primenette = 0;
            $comtotale = 0;
            $comnette = 0;
            $retrocom = 0;
            $importettaxe = 0;
            $codeMonnaie = "";
            foreach ($resultFinal as $police) {
                $primetotale += $police->getPrimeTotale();
                $primenette += $police->getPrimeNette();

                $ricom = $police->getRiCom();
                $localcom = $police->getLocalCom();
                $frontingcom = $police->getFrontingCom();

                if($police->getMonnaie()){
                    $codeMonnaie = $police->getMonnaie()->getCode();
                }
                //dd($frontingcom);

                $netCom = ($ricom + $localcom + $frontingcom);
                $tva = $netCom * (16/100);
                $arca = $netCom * (2/100);
                $comtotale += $netCom + $tva;



                $retro_ricom = 0;
                $retro_localcom = 0;
                $retro_frontingcom = 0;

                if($police->isCansharericom() == true){
                    $retro_ricom += $ricom;
                }
                if($police->isCansharelocalcom() == true){
                    $retro_localcom += $localcom;
                }
                if($police->isCansharefrontingcom() == true){
                    $retro_frontingcom += $frontingcom;
                }

                $taux_retro_com = 0;
                //dd($police->getPartenaire()->getPart());
                if($police->getPartenaire()){
                    $taux_retro_com = $police->getPartenaire()->getPart();
                }

                $retrocom += ($retro_ricom + $retro_localcom + $retro_frontingcom) * ($taux_retro_com / 100);

                $comnette += $netCom - $arca - $retrocom;

                $importettaxe += ($arca + $tva);
            }
            //PRIMES
            $agregat->setPrimeTotale($primetotale);
            $agregat->setPrimeNette($primenette);
            $agregat->setCodeMonnaie($codeMonnaie);
            //COMMISSIONS
            $agregat->setCommissionTotale($comtotale);
            $agregat->setCommissionNette($comnette);
            //PARTENAIRES
            $agregat->setRetroCommissionTotale($retrocom);
            //IMPOTS et TAXES
            $agregat->setImpotEtTaxeTotale($importettaxe);
        }
        return $resultFinal;
    }
}
