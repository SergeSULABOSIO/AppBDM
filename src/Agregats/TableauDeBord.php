<?php

namespace App\Agregats;

use App\Entity\Police;
use Doctrine\ORM\Query\Expr\Func;
use App\Repository\TaxeRepository;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ContactRepository;
use App\Repository\MonnaieRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Repository\AutomobileRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\PartenaireRepository;
use SebastianBergmann\Environment\Console;
use App\Repository\PaiementCommissionRepository;
use App\Repository\OutstandingCommissionRepository;
use DateTime;

class TableauDeBord
{
    
    public function __construct(
        private PaiementCommissionRepository $paiementCommissionRepository,
        private AssureurRepository $assureurRepository,
        private AutomobileRepository $automobileRepository,
        private ClientRepository $clientRepository,
        private ContactRepository $contactRepository,
        private EntrepriseRepository $entrepriseRepository,
        private TaxeRepository $taxeRepository,
        private MonnaieRepository $monnaieRepository,
        private PartenaireRepository $partenaireRepository,
        private PoliceRepository $policeRepository,
        private ProduitRepository $produitRepository,
        private OutstandingCommissionRepository $outstandingCommissionRepository,
        private $polices,
        private $criteres_dashboard
    )
    {
        
    }




    public function dash_get_graphique_fronting_mois(){
        $data_fronting_mois[] = 170;
        $data_fronting_mois[] = 170;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 2500;
        $data_fronting_mois[] = 5000;
        $data_fronting_mois[] = 7500;
        $data_fronting_mois[] = 910;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 50;
        $data_fronting_mois[] = 150;
        $data_fronting_mois[] = 2000;
        return $data_fronting_mois;
    }


    public function dash_get_graphique_primes_ht_mois(){
        $data_primes_ht_mois[] = 1000;
        $data_primes_ht_mois[] = 1000;
        $data_primes_ht_mois[] = 21500;
        $data_primes_ht_mois[] = 28000;
        $data_primes_ht_mois[] = 52000;
        $data_primes_ht_mois[] = 70500;
        $data_primes_ht_mois[] = 9100;
        $data_primes_ht_mois[] = 8500;
        $data_primes_ht_mois[] = 30000;
        $data_primes_ht_mois[] = 6050;
        $data_primes_ht_mois[] = 9500;
        $data_primes_ht_mois[] = 20000;
        return $data_primes_ht_mois;
    }


    public function dash_get_graphique_primes_ttc_mois(){
        $data_primes_ttc_mois[] = 15000;
        $data_primes_ttc_mois[] = 1500;
        $data_primes_ttc_mois[] = 24500;
        $data_primes_ttc_mois[] = 30000;
        $data_primes_ttc_mois[] = 60000;
        $data_primes_ttc_mois[] = 73500;
        $data_primes_ttc_mois[] = 9500;
        $data_primes_ttc_mois[] = 10000;
        $data_primes_ttc_mois[] = 35000;
        $data_primes_ttc_mois[] = 6550;
        $data_primes_ttc_mois[] = 11500;
        $data_primes_ttc_mois[] = 23000;
        return $data_primes_ttc_mois;
    }


    public function dash_get_graphique_commissions_impayees_assureur(){
        // $data_com_impayees[] = [
        //     'label' => 'SFA',
        //     'data' => 85000,
        //     'color'=> 'blue'
        // ];
        // $data_com_impayees[] = [
        //     'label' => 'RAWSUR',
        //     'data' => 65000,
        //     'color'=> 'gray'
        // ];
        // $data_com_impayees[] = [
        //     'label' => 'MAYFAIR',
        //     'data' => 5000,
        //     'color'=> 'red'
        // ];
        // $data_com_impayees[] = [
        //     'label' => 'SUNU',
        //     'data' => 150,
        //     'color'=> 'green'
        // ];

        $agregats = new OutstandingCommissionAgregat();
        $taxes = $this->taxeRepository->findAll();
        
        if ($this->criteres_dashboard['assureur'] == null) {
            $ancien_assureur_selected = $this->criteres_dashboard['assureur'];
            foreach ($this->assureurRepository->findAll() as $assureur) {
                $this->criteres_dashboard['assureur'] = $assureur;
                $data = $this->outstandingCommissionRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
                //dd($agregats);
                $data_com_impayees[] = [
                    'label' => $assureur->getNom(),
                    'data' => $agregats->getMontant(),
                    'color'=> $this->getCouleur()
                ];
            }
            $this->criteres_dashboard['assureur'] = $ancien_assureur_selected;
        } else {
            $data = $this->outstandingCommissionRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
            //dd($agregats);
            $data_com_impayees[] = [
                'label' => $this->criteres_dashboard['assureur']->getNom(),
                'data' => $agregats->getMontant(),
                'color'=> $this->getCouleur()
            ];
        }
        //dd($data_com_impayees);
        return $data_com_impayees;
    }


    public function dash_get_graphique_commissions_impayees_mois(){
        $data_com_impayees_mois[] = 13500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 5000;
        $data_com_impayees_mois[] = 5000;
        $data_com_impayees_mois[] = 1500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 5500;
        $data_com_impayees_mois[] = 29000;
        $data_com_impayees_mois[] = 0;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 2000;
        return $data_com_impayees_mois;
    }


    public function dash_get_graphique_commissions_encaissees_mois(){
         $data_com_encaissees_mois[] = 15000;
        // $data_com_encaissees_mois[] = 2000;
        // $data_com_encaissees_mois[] = 25000;
        // $data_com_encaissees_mois[] = 35000;
        // $data_com_encaissees_mois[] = 65000;
        // $data_com_encaissees_mois[] = 75000;
        // $data_com_encaissees_mois[] = 10000;
        // $data_com_encaissees_mois[] = 15500;
        // $data_com_encaissees_mois[] = 64000;
        // $data_com_encaissees_mois[] = 6550;
        // $data_com_encaissees_mois[] = 12000;
        // $data_com_encaissees_mois[] = 25000;

        //$agregats = new PopCommissionAgregat();
        //l'objet critère a besoin d'un champ Police, même vide / null.
        $this->criteres_dashboard['police'] = null;
        $data_paiements_commissions = $this->paiementCommissionRepository->findByMotCle($this->criteres_dashboard, null);
        //dd($data_paiements_commissions);
        //dd($this->criteres_dashboard);
        //de janvier à décembre [0 - 11]
        for ($i=0; $i < 12; $i++) {
            foreach ($data_paiements_commissions as $com_encaissee) {
                //DateTime $date_paiement = new DateTime($com_encaissee->getDate());
                //strtotime("10:30pm April 15 2014");
                //dd(date("m", strtotime("now")));
                dd($com_encaissee->getDate());
            }
            
        }

        return $data_com_encaissees_mois;
    }

    public function dash_get_graphique_commissions_nettes_mois(){
        $data_com_nettes_mois[] = 1500;
        $data_com_nettes_mois[] = 1500;
        $data_com_nettes_mois[] = 24500;
        $data_com_nettes_mois[] = 30000;
        $data_com_nettes_mois[] = 60000;
        $data_com_nettes_mois[] = 73500;
        $data_com_nettes_mois[] = 9500;
        $data_com_nettes_mois[] = 10000;
        $data_com_nettes_mois[] = 35000;
        $data_com_nettes_mois[] = 6550;
        $data_com_nettes_mois[] = 11500;
        $data_com_nettes_mois[] = 23000;
        return $data_com_nettes_mois;
    }


    public function dash_get_graphique_commissions_nettes_assureur(){
        $agregats = new PoliceAgregat();
        $taxes = $this->taxeRepository->findAll();
        
        if ($this->criteres_dashboard['assureur'] == null) {
            $ancien_assureur_selected = $this->criteres_dashboard['assureur'];
            foreach ($this->assureurRepository->findAll() as $assureur) {
                $this->criteres_dashboard['assureur'] = $assureur;
                $data = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
                //dd($agregats);
                $data_com_nettes[] = [
                    'label' => $assureur->getNom(),
                    'data' => $agregats->getCommissionNette(),
                    'color'=> $this->getCouleur()
                ];
            }
            $this->criteres_dashboard['assureur'] = $ancien_assureur_selected;
        } else {
            $data = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
            //dd($agregats);
            $data_com_nettes[] = [
                'label' => $this->criteres_dashboard['assureur']->getNom(),
                'data' => $agregats->getCommissionNette(),
                'color'=> $this->getCouleur()
            ];
        }
        //dd($data_com_nettes);
        return $data_com_nettes;
    }


    public function dash_get_graphique_primes_assureur(){
        $data_primes_assureur = null;
        
        foreach ($this->assureurRepository->findAll() as $assureur) {
            $label = $assureur->getNom();
            $data = 0;
            $color = $this->getCouleur();

            foreach ($this->polices as $police) {
                //dd($police->getAssureur());
                if($police->getAssureur() == $assureur){
                    $data += $police->getPrimeTotale();
                }
            }
            $data_primes_assureur[] = [
                'label' => $label,
                'data' => $data,
                'color'=> $color
            ];
        }
        //dd($data_primes_assureur);
        return $data_primes_assureur;
    }

    function getCouleur()
    {
        //return 'rgb(' . rand(128, 220) . ',' . rand(128, 225) . ',' . rand(128, 225) . ')'; #using the inbuilt random function
        return 'rgb(' . rand(0, 140) . ',' . rand(0, 128) . ',' . rand(0, 128) . ')';
    }



    public function dash_get_nb_enregistrements(){
        $data_nb_enregistrements["assureurs"] = [
            "valeur" => $this->assureurRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["automobiles"] = [
            "valeur" => $this->automobileRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["clients"] = [
            "valeur" => $this->clientRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["contacts"] = [
            "valeur" => $this->contactRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["entreprises"] = [
            "valeur" => $this->entrepriseRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["impots_et_taxes"] = [
            "valeur" => $this->taxeRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["monnaies"] = [
            "valeur" => $this->monnaieRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["partenaires"] = [
            "valeur" => $this->partenaireRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["polices"] = [
            "valeur" => $this->policeRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];
        $data_nb_enregistrements["produits"] = [
            "valeur" => $this->produitRepository->stat_get_nombres_enregistrements(),
            "limit" => null
        ];

        return $data_nb_enregistrements;
    }

}