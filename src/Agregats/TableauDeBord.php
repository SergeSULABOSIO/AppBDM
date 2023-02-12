<?php

namespace App\Agregats;

use App\Entity\Police;
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
use Doctrine\ORM\Query\Expr\Func;

class TableauDeBord
{
    
    public function __construct(
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
        private $polices,
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
        $data_com_impayees[] = [
            'label' => 'SFA',
            'data' => 85000,
            'color'=> 'blue'
        ];
        $data_com_impayees[] = [
            'label' => 'RAWSUR',
            'data' => 65000,
            'color'=> 'gray'
        ];
        $data_com_impayees[] = [
            'label' => 'MAYFAIR',
            'data' => 5000,
            'color'=> 'red'
        ];
        $data_com_impayees[] = [
            'label' => 'SUNU',
            'data' => 150,
            'color'=> 'green'
        ];
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
        $data_com_encaissees_mois[] = 2000;
        $data_com_encaissees_mois[] = 25000;
        $data_com_encaissees_mois[] = 35000;
        $data_com_encaissees_mois[] = 65000;
        $data_com_encaissees_mois[] = 75000;
        $data_com_encaissees_mois[] = 10000;
        $data_com_encaissees_mois[] = 15500;
        $data_com_encaissees_mois[] = 64000;
        $data_com_encaissees_mois[] = 6550;
        $data_com_encaissees_mois[] = 12000;
        $data_com_encaissees_mois[] = 25000;
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
        $data_com_nettes[] = [
            'label' => 'SFA',
            'data' => 85000,
            'color'=> 'blue'
        ];
        $data_com_nettes[] = [
            'label' => 'RAWSUR',
            'data' => 65000,
            'color'=> 'gray'
        ];
        $data_com_nettes[] = [
            'label' => 'MAYFAIR',
            'data' => 50000,
            'color'=> 'red'
        ];
        $data_com_nettes[] = [
            'label' => 'SUNU',
            'data' => 12000,
            'color'=> 'green'
        ];
        return $data_com_nettes;
    }


    public function dash_get_graphique_primes_assureur(){
        // $data_primes_assureur[] = [
        //     'label' => 'SFA',
        //     'data' => 850000,
        //     'color'=> 'blue'
        // ];
        // $data_primes_assureur[] = [
        //     'label' => 'RAWSUR',
        //     'data' => 650000,
        //     'color'=> 'gray'
        // ];
        // $data_primes_assureur[] = [
        //     'label' => 'MAYFAIR',
        //     'data' => 50000,
        //     'color'=> 'red'
        // ];
        // $data_primes_assureur[] = [
        //     'label' => 'SUNU',
        //     'data' => 12000,
        //     'color'=> 'green'
        // ];
        //dd($this->polices);

        $data_primes_assureur = null;
        
        foreach ($this->assureurRepository->findAll() as $assureur) {
            $label = $assureur->getNom();
            $data = 0;
            $color = "green";
            foreach ($this->polices as $police) {
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
        

        return $data_primes_assureur;
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
