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
use App\Repository\PaiementCommissionRepository;
use App\Repository\OutstandingCommissionRepository;
use App\Repository\PaiementPartenaireRepository;
use App\Outstanding\RetrocomOutstanding;
use App\Repository\PaiementTaxeRepository;

class TableauDeBord
{
    //titres pour production
    private $ttr_GRAND_TOTAL = "Grand Total";
    private $ttr_PRIMES_TTC = "Primes Totale";
    private $ttr_COM_HT = "Com. hors taxe";
    private $ttr_COM_TTC = "Commission ttc";
    private $ttr_COM_ENCAISSEE = "Com. encaissées";
    private $ttr_SOLDE_DU = "Solde à recevoir/Com.";
    private $ttr_RETRO_COM_DUE = "Retrocom. dues";
    private $ttr_RETRO_COM_PAYEE = "Retrocom. payée";
    private $ttr_RETRO_SOLDE_DU = "Solde à payer/Retrocom";
    private $ttr_TAXES_SOLDE_A_PAYER = "Solde à payer/Taxe";
    private $ttr_TAXES_TAXE_DUE = "Taxe due";
    private $ttr_TAXES_PAYEE = "Taxe payée";
    


    private $tab_MOIS_ANNEE = [
        "JANVIER", 
        "FEVRIER", 
        "MARS", 
        "AVRIL", 
        "MAI", 
        "JUIN", 
        "JUILLET", 
        "AOUT", 
        "SEPTEMBRE", 
        "OCTOBRE", 
        "NOVEMBRE", 
        "DECEMBRE"
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
        private PaiementPartenaireRepository $paiementPartenaireRepository,
        private PaiementTaxeRepository $paiementTaxeRepository,
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
        $production_assureur['titres'][] = "Assureurs";
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
        //1 - filtre par assureur
        foreach ($assureurs as $assureur) {
            $lignes = null;
            $primes_ttc_assureur = 0;
            $com_ht_assureur = 0;
            $tab_taxes_assureur = [];
            foreach ($taxes as $taxe) {
                $tab_taxes_assureur[$taxe->getNom()] = 0;
            }
            $com_ttc_assureur = 0;
            $com_encaissee_assureur = 0;
            $solde_du_assureur = 0;
            //2 - filtre pour chaque mois de l'année
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
                //3 - filtre par police
                foreach ($this->polices as $police) {
                    if($police->getAssureur() == $assureur){
                        
                        $date_mois_police = $police->getDateEffet()->format("m");
                        //dd($date_police);
                        if($date_mois_police == ($i + 1)){                            
                            $details_police = $this->_prod_production_get_details($police, $taxes);
                            $prime_ttc_mois += $details_police["police_prime_ttc"];
                            $com_ht_mois += $details_police["police_com_ht"];
                            $tab_taxes_mois = $details_police["police_com_tab_taxes"];
                            $com_encaissee_mois += $details_police["police_com_encaissee"];
                            $com_ttc_mois += $details_police["police_com_ttc"];
                            $solde_du_mois += $details_police["police_com_solde_due"];
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
                //ici on doit charger les taxes
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
        $assureurs = $this->assureurRepository->findAll();
        $taxes = $this->taxeRepository->findAll();
        //on prévoit quand-même un tableau vide pour servir d'exemple
        // $production_mois = [
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

        // dd($production_mois);
        $production_mois['titres'][] = "Mois";
        $production_mois['titres'][] = $this->ttr_PRIMES_TTC;
        $production_mois['titres'][] = $this->ttr_COM_HT;
        //On charge les taxes automatiquement depuis un tableau - Ici le contenu du tableau peut varier
        foreach ($taxes as $taxe) {
            $production_mois['titres'][] = $taxe . " @" . ($taxe->getTaux()) . "%";
        }
        $production_mois['titres'][] = $this->ttr_COM_TTC;
        $production_mois['titres'][] = $this->ttr_COM_ENCAISSEE;
        $production_mois['titres'][] = $this->ttr_SOLDE_DU;
        //dd($production_mois);
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
        //1- filtre par mois
        for ($i=0; $i < 12; $i++) {
            $lignes = null;
            $prime_ttc_mois = 0;
            $com_ht_mois = 0;
            $tab_taxes_mois = [];
            foreach ($taxes as $taxe) {
                $tab_taxes_mois[$taxe->getNom()] = 0;
            }
            $com_ttc_mois = 0;
            $com_encaissee_mois = 0;
            $solde_du_mois = 0;
            //2 - filtre pour chaque assureur
            foreach ($assureurs as $assureur) {
                $prime_ttc_assureur = 0;
                $com_ht_assureur = 0;
                $tab_taxes_assureur = [];
                foreach ($taxes as $taxe) {
                    $tab_taxes_assureur[$taxe->getNom()] = 0;
                }
                $com_ttc_assureur = 0;
                $com_encaissee_assureur = 0;
                $solde_du_assureur = 0;
                //3 - filtre par police
                foreach ($this->polices as $police) {
                    if($police->getAssureur() == $assureur){
                        $date_mois_police = $police->getDateEffet()->format("m");
                        //dd($date_police);
                        if($date_mois_police == ($i + 1)){
                            $details_police = $this->_prod_production_get_details($police, $taxes);
                            $prime_ttc_assureur += $details_police["police_prime_ttc"];
                            $com_ht_assureur += $details_police["police_com_ht"];
                            $tab_taxes_assureur = $details_police["police_com_tab_taxes"];
                            $com_encaissee_assureur += $details_police["police_com_encaissee"];
                            $com_ttc_assureur += $details_police["police_com_ttc"];
                            $solde_du_assureur += $details_police["police_com_solde_due"];
                        }
                    }
                }
                if($prime_ttc_assureur != 0){
                    $prime_ttc_mois += $prime_ttc_assureur;
                    $com_ht_mois += $com_ht_assureur;
                    foreach ($taxes as $taxe) {
                        $tab_taxes_mois[$taxe->getNom()] = $tab_taxes_mois[$taxe->getNom()] + $tab_taxes_assureur[$taxe->getNom()];
                    }
                    $com_ttc_mois += $com_ttc_assureur;
                    $com_encaissee_mois += $com_encaissee_assureur;
                    $solde_du_mois += $solde_du_assureur;
    
                    $data_ligne_assureur = [];
                    $data_ligne_assureur[] = $assureur->getNom();
                    $data_ligne_assureur[] = $prime_ttc_assureur;
                    $data_ligne_assureur[] = $com_ht_assureur;
                    foreach ($taxes as $taxe) {
                        $data_ligne_assureur[] = $tab_taxes_assureur[$taxe->getNom()];
                    }
                    $data_ligne_assureur[] = $com_ttc_assureur;
                    $data_ligne_assureur[] = $com_encaissee_assureur;
                    $data_ligne_assureur[] = $solde_du_assureur;
                    $ligne_assureur = $data_ligne_assureur;
                    $lignes[] = $ligne_assureur;
                }
            }
            //chargement des données - chargement des sous totaux
            if($prime_ttc_mois != 0){
                $data_sous_total = [];
                $data_sous_total[] = $this->tab_MOIS_ANNEE[$i];
                $data_sous_total[] = $prime_ttc_mois;
                $data_sous_total[] = $com_ht_mois;
                //ici on doit charger les taxes
                foreach ($taxes as $taxe) {
                    $data_sous_total[] = $tab_taxes_mois[$taxe->getNom()];
                    $tab_taxes_grand_total[$taxe->getNom()] = $tab_taxes_grand_total[$taxe->getNom()] + $tab_taxes_mois[$taxe->getNom()];
                }
                $data_sous_total[] = $com_ttc_mois;
                $data_sous_total[] = $com_encaissee_mois;
                $data_sous_total[] = $solde_du_mois;
                $sous_total = $data_sous_total;
                //chargement des données - chargement des lignes
                $production_mois['donnees'][] = [
                    'sous_total' => $sous_total,
                    'lignes' => $lignes
                ];
                $prime_ttc_grand_total += $prime_ttc_mois;
                $com_ht_grand_total += $com_ht_mois;
                $com_ttc_grand_total += $com_ttc_mois;
                $com_encaissee_grand_total += $com_encaissee_mois;
                $solde_du_grand_total += $solde_du_mois;
            } 
        }
        $data_production_mois = [];
        $data_production_mois[] = $this->ttr_GRAND_TOTAL;
        $data_production_mois[] = $prime_ttc_grand_total;
        $data_production_mois[] = $com_ht_grand_total;
        foreach ($taxes as $taxe) {
            $data_production_mois[] = $tab_taxes_grand_total[$taxe->getNom()];
        }
        $data_production_mois[] = $com_ttc_grand_total;
        $data_production_mois[] = $com_encaissee_grand_total;
        $data_production_mois[] = $solde_du_grand_total;
        $production_mois['totaux'] = $data_production_mois;
        return $production_mois;
    }


    private function _prod_part_getTitres($taxes)
    {
        //dd($production_assureur);
        $production_partenaire['titres'][] = "Partenaires";
        $production_partenaire['titres'][] = $this->ttr_COM_TTC;
        //On charge les taxes automatiquement depuis un tableau - Ici le contenu du tableau peut varier
        foreach ($taxes as $taxe) {
            $production_partenaire['titres'][] = "- " . $taxe . " @" . ($taxe->getTaux()) . "%";
        }
        $production_partenaire['titres'][] = $this->ttr_COM_HT;
        $production_partenaire['titres'][] = $this->ttr_RETRO_COM_DUE;
        $production_partenaire['titres'][] = "- " . $this->ttr_RETRO_COM_PAYEE;
        $production_partenaire['titres'][] = $this->ttr_RETRO_SOLDE_DU;

        return $production_partenaire;
    }

    private function _prod_taxes_getTitres()
    {
        $production_taxes['titres'][] = "Type des Taxes / Impôts";
        //$production_taxes['titres'][] = $this->ttr_COM_TTC;
        //$production_taxes['titres'][] = $this->ttr_COM_HT;
        $production_taxes['titres'][] = $this->ttr_TAXES_TAXE_DUE;
        $production_taxes['titres'][] = "- " . $this->ttr_TAXES_PAYEE;
        $production_taxes['titres'][] = $this->ttr_TAXES_SOLDE_A_PAYER;

        return $production_taxes;
    }


    private function _prod_partenaire_get_details($police, $taxes)
    {
        $tab_details = [
            'com_ttc_police' => 0,            //montant encaissé
            'com_ht_police' => 0,               //montant hors taxes partageable
            'retrocom_due_police' => 0,         //retrocommission dûe au partenaire selon sa part (%)
            'retrocom_payee_police' => 0,       //retrocommission payée déjà au partenaire
            'retrocom_solde_due_police' => 0,   //Solde dû au partenaire
        ];
        //On va vérifier aussi les paiements possibles
        $data_paiementsRetroCommissions = $this->paiementPartenaireRepository->findByMotCle([
            'dateA' => "",
            'dateB' => "",
            'motcle' => "",
            'police' => $police,
            'assureur' => null,
            'client' => $police->getClient(),
            'partenaire' => $police->getPartenaire()
        ], null);
        $retrocommOustanding = new RetrocomOutstanding($police, $data_paiementsRetroCommissions, $taxes);
        //return $retrocommOustanding->montantNetPartageable;
        $tab_details['com_ttc_police'] = $retrocommOustanding->montantNetPartageable_ttc;
        $tab_details['com_ht_police'] = $retrocommOustanding->montantNetPartageable_ht;
        $tab_details['retrocom_due_police'] = $retrocommOustanding->montantDu;
        $tab_details['retrocom_payee_police'] = $retrocommOustanding->montantDecaisse;
        $tab_details['retrocom_solde_due_police'] = $retrocommOustanding->montantSolde;//tab_taxes_mois
        $tab_details['com_tab_taxes'] = $retrocommOustanding->tab_montants_taxes;
        return $tab_details;
    }



    private function _prod_production_get_details($police, $taxes)
    {
        $tab_details = [
            'police_prime_ttc' => 0,
            'police_com_ttc' => 0,    
            'police_com_ht' => 0, 
            'police_com_tab_taxes' => [],           
            'police_com_encaissee' => 0,
            'police_com_solde_due' => 0
        ];
        
        $aggregat_police = new PoliceAgregatCalculator($police, $taxes);

        //dd($aggregat_police);

        $tab_taxes_police = [];
        
        foreach ($taxes as $taxe) {
            $montant_taxe_police = 0;
            foreach ($aggregat_police->getTab_Taxes() as $taxes_polices) {
                if($taxes_polices['nom'] == $taxe->getNom()){
                    $montant_taxe_police = $taxes_polices['montant'];
                }
            }
            if (array_key_exists($taxe->getNom(), $tab_taxes_police)) {
                $val_taxe_existant = $tab_taxes_police[$taxe->getNom()] + $montant_taxe_police;
            }else{
                $val_taxe_existant = $montant_taxe_police;
            }
            $tab_taxes_police[$taxe->getNom()] = $val_taxe_existant;
        }
        
        //encaissements - recherche
        $comReceived = 0;
        $tab_com_encaissees = $this->paiementCommissionRepository->findByMotCle([
            'police' => $police,
            'client' => $police->getClient(),
            'assureur' => $police->getAssureur(),
            'partenaire' => $police->getPartenaire(),
            'motcle' => "",
            'dateA' => null,
            'dateB' => null
        ], null);
        foreach ($tab_com_encaissees as $encaissement) {
            $comReceived += $encaissement->getMontant();
        }
        
        $tab_details['police_prime_ttc'] = $aggregat_police->getPrimeTotale();
        $tab_details['police_com_ht'] = $aggregat_police->getCommissionNette();
        $tab_details['police_com_ttc'] = $aggregat_police->getCommissionTotale();
        $tab_details['police_com_tab_taxes'] = $tab_taxes_police;
        $tab_details['police_com_encaissee'] = ($comReceived);
        $tab_details['police_com_solde_due'] = ($aggregat_police->getCommissionTotale() - $comReceived);

        return $tab_details;
    }


    private function _prod_taxes_get_details($police, $taxe, $montant_taxes_due)
    {
        $tab_details = [
            'montant_taxe_payee' => 0,            //montant paye
            'montant_taxe_solde_a_payer' => 0     //Solde à payer
        ];

        $agregat_popTaxe = new PopTaxeAgregat();
        //On va vérifier aussi les paiements possibles
        $data_popTaxes = $this->paiementTaxeRepository->findByMotCle([
            'dateA' => "",
            'dateB' => "",
            'motcle' => "",
            'police' => $police,
            'taxe' => $taxe
        ], $agregat_popTaxe);

        $tab_details['montant_taxe_payee'] = $agregat_popTaxe->getMontant();
        $tab_details['montant_taxe_solde_a_payer'] = ($montant_taxes_due - $agregat_popTaxe->getMontant());
        return $tab_details;
    }



    public function dash_get_synthse_retrocommissoins_mois()
    {
        $partenaires = $this->partenaireRepository->findAll();
        $taxes = $this->taxeRepository->findAll();
        
        $production_partenaire = $this->_prod_part_getTitres($taxes);
        //dd($production_assureur);
        $com_recue_grand_total = 0;
        $tab_taxes_grand_total = [];
        foreach ($taxes as $taxe) {
            $tab_taxes_grand_total[$taxe->getNom()] = 0;
        }
        //dd($tab_taxes_grand_total);
        $com_ht_grand_total = 0;
        $com_due_grand_total = 0;
        $com_payee_grand_total = 0;
        $solde_du_grand_total = 0;
        //1 - filtre par partenaire
        foreach ($partenaires as $partenaire) {
            $lignes = null;
            $com_recue_partenaire = 0;
            $tab_taxes_partenaire = [];
            foreach ($taxes as $taxe) {
                $tab_taxes_partenaire[$taxe->getNom()] = 0;
            }
            $com_ht_partenaire = 0;
            $com_due_partenaire = 0;
            $com_payee_partenaire = 0;
            $solde_du_partenaire = 0;
            //2 - filtre pour chaque mois de l'année
            for ($i=0; $i < 12; $i++) {
                $com_recue_mois = 0;
                $tab_taxes_mois = [];
                foreach ($taxes as $taxe) {
                    $tab_taxes_mois[$taxe->getNom()] = 0;
                }
                $com_ht_mois = 0;
                $com_due_mois = 0;
                $com_payee_mois = 0;
                $solde_du_mois = 0;
                //3 - filtre par police
                foreach ($this->polices as $police) {
                    if($police->getPartenaire() == $partenaire){
                        $date_mois_police = $police->getDateEffet()->format("m");
                        //ici il faut calculer la veleur réelle de la taxe puis l'ajouter dans la table
                        if($date_mois_police == ($i + 1)){
                            foreach ($taxes as $taxe) {
                                $tab_taxes_mois[$taxe->getNom()] = $tab_taxes_mois[$taxe->getNom()];
                            }
                            $tab_details = $this->_prod_partenaire_get_details($police, $taxes);
                            
                            $tab_taxes_mois = $tab_details['com_tab_taxes'];
                            $com_recue_mois += $tab_details['com_ttc_police'];
                            $com_ht_mois += $tab_details['com_ht_police'];
                            $com_due_mois += $tab_details['retrocom_due_police'];
                            $com_payee_mois += $tab_details['retrocom_payee_police'];
                            $solde_du_mois += $tab_details['retrocom_solde_due_police'];
                        }
                    }
                }
                if($com_recue_mois != 0){
                    $com_recue_partenaire += $com_recue_mois;
                    $com_ht_partenaire += $com_ht_mois;
                    $com_due_partenaire += $com_due_mois;
                    $com_payee_partenaire += $com_payee_mois;
                    $solde_du_partenaire += $solde_du_mois;
                    //on charge les taxes
                    foreach ($taxes as $taxe) {
                        $tab_taxes_partenaire[$taxe->getNom()] = $tab_taxes_partenaire[$taxe->getNom()] + $tab_taxes_mois[$taxe->getNom()];
                    }
                    
                    $data_ligne_mois = [];
                    $data_ligne_mois[] = $this->tab_MOIS_ANNEE[$i];
                    $data_ligne_mois[] = $com_recue_mois;
                    foreach ($taxes as $taxe) {
                        $data_ligne_mois[] = $tab_taxes_mois[$taxe->getNom()];
                    }
                    $data_ligne_mois[] = $com_ht_mois;
                    $data_ligne_mois[] = $com_due_mois;
                    $data_ligne_mois[] = $com_payee_mois;
                    $data_ligne_mois[] = $solde_du_mois;
                    $ligne_mois = $data_ligne_mois;
                    $lignes[] = $ligne_mois;
                }
            }
            //chargement des données - chargement des sous totaux
            if($com_recue_partenaire != 0){
                $data_sous_total = [];
                $data_sous_total[] = $partenaire->getNom() . " [@". $partenaire->getPart() ."%]";
                $data_sous_total[] = $com_recue_partenaire;
                //ici on doit charger les taxes
                foreach ($taxes as $taxe) {
                    $data_sous_total[] = $tab_taxes_partenaire[$taxe->getNom()];
                    $tab_taxes_grand_total[$taxe->getNom()] = $tab_taxes_grand_total[$taxe->getNom()] + $tab_taxes_partenaire[$taxe->getNom()];
                }
                $data_sous_total[] = $com_ht_partenaire;
                $data_sous_total[] = $com_due_partenaire;
                $data_sous_total[] = $com_payee_partenaire;
                $data_sous_total[] = $solde_du_partenaire;
                $sous_total = $data_sous_total;
                //chargement des données - chargement des lignes
                $production_partenaire['donnees'][] = [
                    'sous_total' => $sous_total,
                    'lignes' => $lignes
                ];
                $com_recue_grand_total += $com_recue_partenaire;
                $com_ht_grand_total += $com_ht_partenaire;
                $com_due_grand_total += $com_due_partenaire;
                $com_payee_grand_total += $com_payee_partenaire;
                $solde_du_grand_total += $solde_du_partenaire;
            }
        }
        $data_production_partenaire = [];
        $data_production_partenaire[] = $this->ttr_GRAND_TOTAL;
        $data_production_partenaire[] = $com_recue_grand_total;
        foreach ($taxes as $taxe) {
            $data_production_partenaire[] = $tab_taxes_grand_total[$taxe->getNom()];
        }
        $data_production_partenaire[] = $com_ht_grand_total;
        $data_production_partenaire[] = $com_due_grand_total;
        $data_production_partenaire[] = $com_payee_grand_total;
        $data_production_partenaire[] = $solde_du_grand_total;
        $production_partenaire['totaux'] = $data_production_partenaire;
        return $production_partenaire;
    }

    public function dash_get_synthse_impots_et_taxes_mois()
    {
        $taxes = $this->taxeRepository->findAll();
        //dd($taxes);
        $production_taxe = $this->_prod_taxes_getTitres();
        //dd($production_taxe);
        $grand_total_taxe_due = 0;
        $grand_total_taxe_payee = 0;
        $grand_total_solde_a_payer = 0;
        //1 - filtre par taxe
        foreach ($taxes as $taxe) {
            $tab_lignes_taxes = null;
            $lignes_taxes_taxe_due = 0;
            $lignes_taxes_taxe_payee = 0;
            $lignes_taxes_solde_a_payer = 0;
            //2 - filtre pour chaque mois de l'année
            for ($index_mois=0; $index_mois < 12; $index_mois++) {
                $lignes_taxes_mois_taxe_due = 0;
                $lignes_taxes_mois_taxe_payee = 0;
                $lignes_taxes_mois_solde_a_payer = 0;
                //3 - filtre par police
                foreach ($this->polices as $police) {
                    $date_mois_police = $police->getDateEffet()->format("m");
                    //ici il faut calculer la veleur réelle de la taxe puis l'ajouter dans la table
                    if($date_mois_police == ($index_mois + 1)){
                        //$tab_details_dus = $this->_prod_partenaire_get_details($police, [0 => $taxe]);
                        $tab_details_dus = $this->_prod_production_get_details($police, [0 => $taxe]);
                        $tab_details_pop = $this->_prod_taxes_get_details($police, $taxe, $tab_details_dus['police_com_tab_taxes'][$taxe->getNom()]);
                        $lignes_taxes_mois_taxe_due += $tab_details_dus['police_com_tab_taxes'][$taxe->getNom()];
                        $lignes_taxes_mois_taxe_payee += $tab_details_pop['montant_taxe_payee'];
                        $lignes_taxes_mois_solde_a_payer += $tab_details_pop['montant_taxe_solde_a_payer'];
                    }
                }
                if($lignes_taxes_mois_taxe_due != 0){
                    $lignes_taxes_taxe_due += $lignes_taxes_mois_taxe_due;
                    $lignes_taxes_taxe_payee += $lignes_taxes_mois_taxe_payee;
                    $lignes_taxes_solde_a_payer += $lignes_taxes_mois_solde_a_payer;
                    
                    $data_ligne_mois = [];
                    $data_ligne_mois[] = $this->tab_MOIS_ANNEE[$index_mois];
                    $data_ligne_mois[] = $lignes_taxes_mois_taxe_due;
                    $data_ligne_mois[] = $lignes_taxes_mois_taxe_payee;
                    $data_ligne_mois[] = $lignes_taxes_mois_solde_a_payer;
                    $tab_lignes_taxes[] = $data_ligne_mois;
                }
            }
            //chargement des données - chargement des sous totaux
            if($lignes_taxes_taxe_due != 0){
                $data_sous_total = [];
                $data_sous_total[] = $taxe->getNom(). ", " . $taxe->getOrganisation() . " [taux d'imposition: ". $taxe->getTaux() ."%]";
                $data_sous_total[] = $lignes_taxes_taxe_due;
                $data_sous_total[] = $lignes_taxes_taxe_payee;
                $data_sous_total[] = $lignes_taxes_solde_a_payer;
                $sous_total = $data_sous_total;
                //chargement des données - chargement des lignes
                $production_taxe['donnees'][] = [
                    'sous_total' => $sous_total,
                    'lignes' => $tab_lignes_taxes
                ];

                $grand_total_taxe_due += $lignes_taxes_taxe_due;
                $grand_total_taxe_payee += $lignes_taxes_taxe_payee;
                $grand_total_solde_a_payer += $lignes_taxes_solde_a_payer;
            }
        }
        $data_production_taxe = [];
        $data_production_taxe[] = $this->ttr_GRAND_TOTAL;
        $data_production_taxe[] = $grand_total_taxe_due;
        $data_production_taxe[] = $grand_total_taxe_payee;
        $data_production_taxe[] = $grand_total_solde_a_payer;
        $production_taxe['totaux'] = $data_production_taxe;
        
        //dd($production_taxe);

        return $production_taxe;
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
