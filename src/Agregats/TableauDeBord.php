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
        $assureurs = $this->assureurRepository->findAll();
        $taxes = $this->taxeRepository->findAll();
        //on prévoit quand-même un tableau vide pour servir d'exemple
        // $production_assureur = [
        //     'titres' => [],
        //     'donnees' => [
        //         [
        //             'sous_total' => [],
        //             'lignes' => [
        //                 [],
        //                 []
        //             ]
        //         ]
        //     ],
        //     'totaux' => []
        // ];

        //dd($production_assureur);

        $production_assureur['titres'][] = $this->ttr_ETIQUETTE;
        $production_assureur['titres'][] = $this->ttr_PRIMES_TTC;
        $production_assureur['titres'][] = $this->ttr_COM_HT;
        //On charge les taxes automatiquement depuis un tableau - Ici le contenu du tableau peut varier
        foreach ($taxes as $taxe) {
            $production_assureur['titres'][] = $taxe . " @" . ($taxe->getTaux()) . "%";
        }
        $production_assureur['titres'][] = $this->ttr_COM_TTC;
        $production_assureur['titres'][] = $this->ttr_COM_ENCAISSEE;
        $production_assureur['titres'][] = $this->ttr_SOLDE_DU;
        //dd($production_assureur);
        $prime_ttc_grand_total = 0;
        $com_ht_grand_total = 0;
        $tab_taxes_grand_total = [];
        foreach ($taxes as $taxe) {
            $tab_taxes_grand_total[$taxe->getNom()] = 0;
        }
        //dd($tab_taxes_grand_total);
        $com_ttc_grand_total = 0;
        $com_encaissee_grand_total = 0;
        $solde_du_grand_total = 0;
        //filtre par assureur
        foreach ($assureurs as $assureur) {
            $lignes = null;
            $primes_ttc_assureur = 0;
            $com_ht_assureur = 0;
            $tab_taxes_assureur = [];
            foreach ($taxes as $taxe) {
                $tab_taxes_assureur[$taxe->getNom()] = 0;
            }
            //dd($tab_taxes_assureur);
            $com_ttc_assureur = 0;
            $com_encaissee_assureur = 0;
            $solde_du_assureur = 0;
            //filtre pour chaque mois de l'année
            for ($i=0; $i < 12; $i++) {
                $prime_ttc_mois = 0;
                $com_ht_mois = 0;
                $tab_taxes_mois = [];
                foreach ($taxes as $taxe) {
                    $tab_taxes_mois[$taxe->getNom()] = 0;
                }
                $com_ttc_mois = 0;
                $com_encaissee_mois = 0;
                $solde_du_mois = 0;
                foreach ($this->polices as $police) {
                    if($police->getAssureur() == $assureur){
                        $aggregat_police = new PoliceAgregatCalculator($police, $taxes);
                        $date_mois_police = $police->getDateEffet()->format("m");
                        //dd($date_police);
                        if($date_mois_police == ($i + 1)){
                            $prime_ttc_mois += $aggregat_police->getPrimeTotale();
                            $com_ht_mois += $aggregat_police->getCommissionNette();
                            foreach ($taxes as $taxe) {
                                $montant_taxe_police = 0;
                                foreach ($aggregat_police->getTab_Taxes() as $taxes_polices) {
                                    if($taxes_polices['nom'] == $taxe->getNom()){
                                        //dd($taxes_polices['nom'] . ", " . $taxes_polices['montant']);
                                        $montant_taxe_police = $taxes_polices['montant'];
                                    }
                                }
                                //dd($taxe->getNom(). " = ". $val_taxe_existant);
                                $val_taxe_existant = $tab_taxes_mois[$taxe->getNom()] + $montant_taxe_police;
                                $tab_taxes_mois[$taxe->getNom()] = $val_taxe_existant;
                            }
                            
                            $comTot = $aggregat_police->getCommissionTotale();
                            //encaissements - recherche
                            $comReceived = 0;
                            $tab_com_encaissees = $this->paiementCommissionRepository->findByMotCle([
                                'police' => $police,
                                'client' => $police->getClient(),
                                'assureur' => $assureur,
                                'partenaire' => $police->getPartenaire(),
                                'motcle' => "",
                                'dateA' => null,
                                'dateB' => null
                            ], null);
                            //dd($tab_com_encaissees);
                            foreach ($tab_com_encaissees as $encaissement) {
                                $comReceived += $encaissement->getMontant();
                            }
                            $com_encaissee_mois += $comReceived;
                            $com_ttc_mois += $comTot;
                            $solde_du_mois += ($comTot - $comReceived);
                            //dd($aggregat_police->getTab_Taxes());
                        }
                    }
                }
                if($prime_ttc_mois != 0){
                    $primes_ttc_assureur += $prime_ttc_mois;
                    $com_ht_assureur += $com_ht_mois;
                    foreach ($taxes as $taxe) {
                        $tab_taxes_assureur[$taxe->getNom()] = $tab_taxes_assureur[$taxe->getNom()] + $tab_taxes_mois[$taxe->getNom()];
                    }
                    $com_ttc_assureur += $com_ttc_mois;
                    $com_encaissee_assureur += $com_encaissee_mois;
                    $solde_du_assureur += $solde_du_mois;

                    $data_ligne_mois = [];
                    $data_ligne_mois[] = $this->tab_MOIS_ANNEE[$i];
                    $data_ligne_mois[] = $prime_ttc_mois;
                    $data_ligne_mois[] = $com_ht_mois;
                    foreach ($taxes as $taxe) {
                        $data_ligne_mois[] = $tab_taxes_mois[$taxe->getNom()];
                    }
                    $data_ligne_mois[] = $com_ttc_mois;
                    $data_ligne_mois[] = $com_encaissee_mois;
                    $data_ligne_mois[] = $solde_du_mois;
                    $ligne_mois = $data_ligne_mois;
                    $lignes[] = $ligne_mois;
                }
            }
            //chargement des données - chargement des sous totaux
            if($primes_ttc_assureur != 0){
                $data_sous_total = [];
                $data_sous_total[] = $assureur->getNom();
                $data_sous_total[] = $primes_ttc_assureur;
                $data_sous_total[] = $com_ht_assureur;
                //ici on doit cgarger les taxes
                foreach ($taxes as $taxe) {
                    $data_sous_total[] = $tab_taxes_assureur[$taxe->getNom()];
                    $tab_taxes_grand_total[$taxe->getNom()] = $tab_taxes_grand_total[$taxe->getNom()] + $tab_taxes_assureur[$taxe->getNom()];
                }
                $data_sous_total[] = $com_ttc_assureur;
                $data_sous_total[] = $com_encaissee_assureur;
                $data_sous_total[] = $solde_du_assureur;
                $sous_total = $data_sous_total;
                //chargement des données - chargement des lignes
                $production_assureur['donnees'][] = [
                    'sous_total' => $sous_total,
                    'lignes' => $lignes
                ];
                $prime_ttc_grand_total += $primes_ttc_assureur;
                $com_ht_grand_total += $com_ht_assureur;
                $com_ttc_grand_total += $com_ttc_assureur;
                $com_encaissee_grand_total += $com_encaissee_assureur;
                $solde_du_grand_total += $solde_du_assureur;
            }
        }
        $data_production_assureur = [];
        $data_production_assureur[] = $this->ttr_GRAND_TOTAL;
        $data_production_assureur[] = $prime_ttc_grand_total;
        $data_production_assureur[] = $com_ht_grand_total;
        foreach ($taxes as $taxe) {
            $data_production_assureur[] = $tab_taxes_grand_total[$taxe->getNom()];
        }
        $data_production_assureur[] = $com_ttc_grand_total;
        $data_production_assureur[] = $com_encaissee_grand_total;
        $data_production_assureur[] = $solde_du_grand_total;
        $production_assureur['totaux'] = $data_production_assureur;
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
