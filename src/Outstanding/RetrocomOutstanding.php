<?php

namespace App\Outstanding;

use App\Entity\Police;
use App\Entity\Taxe;

class RetrocomOutstanding
{
    //private ?Police $police = null;
    //private $poppartenaires = [];
    //private $taxes = [];
    public $montantNetPartageable_ht = 0;
    public $montantNetPartageable_ttc = 0;
    public $montantDu = 0;
    public $tab_montants_taxes = [];
    public $montantDecaisse = 0;
    public $montantSolde = 0;
    public $codeMonnaie = "...";
    public $canPay = false;

    public function __construct(
        private ?Police $police, 
        private $poppartenaires,
        private $taxes
        )
    {
        //on initialise le tableau des taxes
        foreach ($this->taxes as $taxe) {
            $this->tab_montants_taxes[$taxe->getNom()] = 0;
        }
        $this->calculateMontantDu();
    }

    public function calculateMontantDu()
    {
        if ($this->police !== null) {
            $net_ri_com = 0;
            $net_local_com = 0;
            $net_fronting_com = 0;

            //on doit vérifier si cette part de commission est partageable ou pas
            if($this->police->isCansharericom()){
                $net_ri_com = $this->police->getRicom();
            }
            if($this->police->isCansharelocalcom()){
                $net_local_com = $this->police->getLocalcom();
            }
            if($this->police->isCansharefrontingcom()){
                $net_fronting_com = $this->police->getFrontingcom();
            }


            $net_including_arca = $net_ri_com + $net_local_com + $net_fronting_com;

            $tot_taxes_payable_par_courtier = 0;
            $tot_taxes_payable_par_assureur = 0;
            foreach ($this->taxes as $taxe) {
                $montant_taxe = $net_including_arca * ($taxe->getTaux() / 100);
                if($taxe->isPayableparcourtier()){
                    $tot_taxes_payable_par_courtier += $montant_taxe;
                }else{
                    $tot_taxes_payable_par_assureur += $montant_taxe;
                }
                $this->tab_montants_taxes[$taxe->getNom()] = $this->tab_montants_taxes[$taxe->getNom()] - $montant_taxe;
            }

            $this->montantNetPartageable_ht = $net_including_arca - $tot_taxes_payable_par_courtier;
            $this->montantNetPartageable_ttc = $net_including_arca + $tot_taxes_payable_par_assureur;
            

            //si le partenaire était défini
            $this->montantDu = 0;
            if($this->police->getPartenaire()){
                $part = ($this->police->getPartenaire()->getPart()) / 100;
                $this->montantDu = $this->montantNetPartageable_ht * $part;
            }
            

            if ($this->police->getMonnaie() != null) {
                $this->codeMonnaie = $this->police->getMonnaie()->getCode();
            }

            //Vérification des com encaissées
            foreach ($this->poppartenaires as $poppartenaire) {
                if ($poppartenaire->getPolice()) {
                    if ($poppartenaire->getPolice()->getId() == $this->police->getId()) {
                        $this->montantDecaisse += $poppartenaire->getMontant();
                    }
                }
            }
            $this->montantSolde = $this->montantDu - $this->montantDecaisse;
            //dd($this);
        }
    }

    public function getPolice()
    {
        return $this->police;
    }

    public function getPopPartenaires()
    {
        return $this->poppartenaires;
    }

    public function isCanPay()
    {
        return $this->canPay;
    }

    public function setCanPay($value)
    {
        $this->canPay = $value;
    }
}
