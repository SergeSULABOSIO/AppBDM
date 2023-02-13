<?php

namespace App\Repository;

use App\Entity\Police;
use App\Agregats\PoliceAgregat;
use Doctrine\Persistence\ManagerRegistry;
use App\Outstanding\CommissionOutstanding;
use App\Agregats\OutstandingCommissionAgregat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Police>
 *
 * @method Police|null find($id, $lockMode = null, $lockVersion = null)
 * @method Police|null findOneBy(array $criteria, array $orderBy = null)
 * @method Police[]    findAll()
 * @method Police[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutstandingCommissionRepository extends ServiceEntityRepository
{
    public function __construct(
        private PoliceRepository $policeRepository,
        private PaiementCommissionRepository $paiementCommissionRepository)
    {

    }

    /**
     * @return Police[] Returns an array of Police objects
     */
    public function findByMotCle($criteres, $agregats, $taxes): array
    {
        $outstandings = [];
        $data = $this->policeRepository->findByMotCle($criteres, null, $taxes);

        $agreg_codeMonnaie = "...";
        $agreg_montant = 0;
        $agreg_montant_net = 0;
        foreach ($data as $police) {
            //On va vérifier aussi les paiements possibles
            $data_paiementsCommissions = $this->paiementCommissionRepository->findByMotCle([
                'dateA' => "",
                'dateB' => "",
                'motcle' => "",
                'police' => $police,
                'assureur' => null,
                'client' => $police->getClient(),
                'partenaire' => $police->getPartenaire()
            ], null);

            $commOustanding = new CommissionOutstanding($police, $data_paiementsCommissions);

            //dd($commOustanding);

            if ($commOustanding->montantSolde != 0) {
                $agreg_montant += $commOustanding->montantSolde;
                $agreg_montant_net += ($commOustanding->montantSolde) / 1.16;
                $agreg_codeMonnaie = $commOustanding->codeMonnaie;
                $outstandings[] = $commOustanding;
            }
        }
        $agregats->setCodeMonnaie($agreg_codeMonnaie);
        $agregats->setMontant($agreg_montant);
        $agregats->setMontantNet($agreg_montant_net);
        //dd($outstandings);



        return $outstandings;
    }

}
