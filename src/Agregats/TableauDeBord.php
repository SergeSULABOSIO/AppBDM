<?php

namespace App\Agregats;

use App\Agregats\PoliceAgregat;
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
use App\Agregats\PoliceAgregatCalculator;
use App\Agregats\OutstandingCommissionAgregat;
use App\Entity\Police;
use App\Repository\PaiementCommissionRepository;
use App\Repository\OutstandingCommissionRepository;

class TableauDeBord
{
    private $ttr_GRAND_TOTAL = "GRAND TOTAL";
    private $ttr_ETIQUETTE = "ETIQUETTE";
    private $ttr_PRIMES_TTC = "PRIMES TTC";
    private $ttr_COM_HT = "COM. HT";
    private $ttr_TVA = "TVA @16%";
    private $ttr_ARCA = "ARCA @2%";
    private $ttr_COM_TTC = "COM. TTC";
    private $ttr_COM_ENCAISSEE = "COM. ENCAISSEE";
    private $ttr_SOLDE_DU = "SOLDE DU";
    private $tab_MOIS_ANNEE = [
        "Janvier", 
        "Févier", 
        "Mars", 
        "Avril", 
        "Mai", 
        "Juin", 
        "Juillet", 
        "Août", 
        "Septembre", 
        "Octobre", 
        "Novembre", 
        "Décembre"
    ]; 

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
    ) {
    }


    public function dash_get_synthse_production_assureur()
    {
        $production_assureur[] = [
            'titres' => [
                $this->ttr_ETIQUETTE,
                $this->ttr_PRIMES_TTC,
                $this->ttr_COM_HT,
                $this->ttr_TVA,
                $this->ttr_ARCA,
                $this->ttr_COM_TTC,
                $this->ttr_COM_ENCAISSEE,
                $this->ttr_SOLDE_DU
            ],
            'donnees' => [
                [
                    'sous-total' => ['ACTIVA', 45000000, 45000000, 45000000, 45000000, 45000000, 45000000, 45000000],
                    'lignes' => [
                        [$this->tab_MOIS_ANNEE[0], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[1], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[2], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[3], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[4], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[5], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[6], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[7], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[8], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[9], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[10], 15000, 4000, 5600, 4500, 3000, 1120, 1000],
                        [$this->tab_MOIS_ANNEE[11], 15000, 4000, 5600, 4500, 3000, 1120, 1000]
                    ]
                ]
            ],
            'totaux' => [$this->ttr_GRAND_TOTAL, 45000000, 45000000, 45000000, 45000000, 45000000, 45000000, 45000000]
        ];
        return $production_assureur;
    }

    public function dash_get_synthse_production_mois()
    {
        $production_mois[] = null;

        return $production_mois;
    }

    public function dash_get_synthse_retrocommissoins_mois()
    {
        $retrocom_mois[] = null;

        return $retrocom_mois;
    }

    public function dash_get_synthse_impots_et_taxes_mois()
    {
        $impots_et_taxes_mois[] = null;

        return $impots_et_taxes_mois;
    }


    public function dash_get_graphique_fronting_mois()
    {
        $agregats = new PoliceAgregat();
        $taxes = $this->taxeRepository->findAll();
        $polices_enregistreees = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
        for ($i = 1; $i <= 12; $i++) {
            $fronting_montant_mensuel = 0;
            foreach ($polices_enregistreees as $police) {
                $mois_police = $police->getDateeffet()->format("m");
                if ($mois_police == $i) {
                    $fronting_montant_mensuel += $police->getFronting();
                }
            }
            $data_fronting_mois[] = $fronting_montant_mensuel;
        }

        return $data_fronting_mois;
    }


    public function dash_get_graphique_primes_ht_mois()
    {
        $agregats = new PoliceAgregat();
        $taxes = $this->taxeRepository->findAll();
        $polices_enregistreees = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
        for ($i = 1; $i <= 12; $i++) {
            $prime_ttc_montant_mensuel = 0;
            foreach ($polices_enregistreees as $police) {
                $mois_police = $police->getDateeffet()->format("m");
                if ($mois_police == $i) {
                    $prime_ttc_montant_mensuel += (new PoliceAgregatCalculator($police, $taxes))->getPrimeNette();
                }
            }
            $data_primes_ht_mois[] = $prime_ttc_montant_mensuel;
        }

        return $data_primes_ht_mois;
    }


    public function dash_get_graphique_primes_ttc_mois()
    {
        $agregats = new PoliceAgregat();
        $taxes = $this->taxeRepository->findAll();
        $polices_enregistreees = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
        for ($i = 1; $i <= 12; $i++) {
            $prime_ttc_montant_mensuel = 0;
            foreach ($polices_enregistreees as $police) {
                $mois_police = $police->getDateeffet()->format("m");
                if ($mois_police == $i) {
                    $prime_ttc_montant_mensuel += (new PoliceAgregatCalculator($police, $taxes))->getPrimeTotale();
                }
            }
            $data_primes_ttc_mois[] = $prime_ttc_montant_mensuel;
        }

        return $data_primes_ttc_mois;
    }


    public function dash_get_graphique_commissions_impayees_assureur()
    {
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
                    'color' => $this->getCouleur()
                ];
            }
            $this->criteres_dashboard['assureur'] = $ancien_assureur_selected;
        } else {
            $data = $this->outstandingCommissionRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
            //dd($agregats);
            $data_com_impayees[] = [
                'label' => $this->criteres_dashboard['assureur']->getNom(),
                'data' => $agregats->getMontant(),
                'color' => $this->getCouleur()
            ];
        }
        //dd($data_com_impayees);
        return $data_com_impayees;
    }


    public function dash_get_graphique_commissions_impayees_mois()
    {
        $agregats = new OutstandingCommissionAgregat();
        $taxes = $this->taxeRepository->findAll();
        $data = $this->outstandingCommissionRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
        
        //dd($agregats);
        for ($i = 1; $i <= 12; $i++) {
            $montant_mensuel = 0;
            foreach ($data as $com_impayee) {
                $mois_impayee = $com_impayee->getPolice()->getDateeffet()->format("m");
                if ($mois_impayee == $i) {
                    $montant_mensuel += $com_impayee->getSoldeDue();
                }
            }
            $data_com_impayees_mois[] = $montant_mensuel;
        }
        return $data_com_impayees_mois;
    }


    public function dash_get_graphique_commissions_encaissees_mois()
    {
        //l'objet critère a besoin d'un champ Police, même vide / null.
        $this->criteres_dashboard['police'] = null;
        $data_paiements_commissions = $this->paiementCommissionRepository->findByMotCle($this->criteres_dashboard, null);
        //de janvier à décembre [0 - 11]
        for ($i = 1; $i <= 12; $i++) {
            $montant_mensuel = 0;
            foreach ($data_paiements_commissions as $com_encaissee) {
                $mois_paiement = $com_encaissee->getDate()->format("m");
                if ($mois_paiement == $i) {
                    $montant_mensuel += $com_encaissee->getMontant();
                }
            }
            $data_com_encaissees_mois[] = $montant_mensuel;
        }
        return $data_com_encaissees_mois;
    }

    public function dash_get_graphique_commissions_nettes_mois()
    {
        $agregats = new PoliceAgregat();
        $taxes = $this->taxeRepository->findAll();
        $polices_enregistreees = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
        for ($i = 1; $i <= 12; $i++) {
            $commission_montant_mensuel = 0;
            foreach ($polices_enregistreees as $police) {
                $mois_police = $police->getDateeffet()->format("m");
                if ($mois_police == $i) {
                    $commission_montant_mensuel += (new PoliceAgregatCalculator($police, $taxes))->getCommissionNette();
                }
            }
            $data_com_nettes_mois[] = $commission_montant_mensuel;
        }

        return $data_com_nettes_mois;
    }


    public function dash_get_graphique_commissions_nettes_assureur()
    {
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
                    'color' => $this->getCouleur()
                ];
            }
            $this->criteres_dashboard['assureur'] = $ancien_assureur_selected;
        } else {
            $data = $this->policeRepository->findByMotCle($this->criteres_dashboard, $agregats, $taxes);
            //dd($agregats);
            $data_com_nettes[] = [
                'label' => $this->criteres_dashboard['assureur']->getNom(),
                'data' => $agregats->getCommissionNette(),
                'color' => $this->getCouleur()
            ];
        }
        //dd($data_com_nettes);
        return $data_com_nettes;
    }


    public function dash_get_graphique_primes_assureur()
    {
        $data_primes_assureur = null;

        foreach ($this->assureurRepository->findAll() as $assureur) {
            $label = $assureur->getNom();
            $data = 0;
            $color = $this->getCouleur();

            foreach ($this->polices as $police) {
                //dd($police->getAssureur());
                if ($police->getAssureur() == $assureur) {
                    $data += $police->getPrimeTotale();
                }
            }
            $data_primes_assureur[] = [
                'label' => $label,
                'data' => $data,
                'color' => $color
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



    public function dash_get_nb_enregistrements()
    {
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
