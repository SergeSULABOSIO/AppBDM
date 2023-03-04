<?php

namespace App\Agregats;

use App\Entity\Police;

class PoliceAgregatCalculator
{
    private $codemonnaie = "";
    private $primenette = 0;
    private $primetotale = 0;
    private $fronting = 0;
    private $commissiontotale = 0;
    private $commissionnette = 0;
    private $retrocommissiontotale = 0;
    private $impotettaxetotale = 0;
    private $tab_taxes = null;

    public function __construct(private Police $police, private $taxes)
    {
        //Calcul des données
        $this->primetotale = $this->police->getPrimeTotale();
        $this->primenette = $this->police->getPrimeNette();
        $this->fronting = $this->police->getFronting();

        //La monnaie
        $this->codemonnaie = $this->police->getMonnaie() ? $this->police->getMonnaie()->getCode() : "...";

        //Commissions
        $ricom = $this->police->getRiCom();
        $localcom = $this->police->getLocalCom();
        $frontingcom = $this->police->getFrontingCom();
        $net_com_including_arca = ($ricom + $localcom + $frontingcom);


        //Commissions partageables
        $ricom_sharable = $this->police->isCansharericom() ? $this->police->getRiCom() : 0;
        $localcom_sharable = $this->police->isCansharelocalcom() ? $this->police->getLocalCom() : 0;
        $frontingcom_sharable = $this->police->isCansharefrontingcom() ? $this->police->getFrontingCom() : 0;
        $net_com_including_arca_sharable = ($ricom_sharable + $localcom_sharable + $frontingcom_sharable);


        //Taxes
        $taxe_charge_assureur = 0;
        $taxe_charge_courtier = 0;
        $taxe_charge_courtier_sharable = 0;

        $impot = 0;

        foreach ($this->taxes as $taxe) {
            $taux = $taxe->getTaux();
            if ($taxe->isPayableparcourtier() == true) {
                $taxe_charge_courtier += $net_com_including_arca * ($taux / 100);
                $taxe_charge_courtier_sharable += $net_com_including_arca_sharable * ($taux / 100);
            } else {
                $taxe_charge_assureur += $net_com_including_arca * ($taux / 100);
            }
            //On cumule le tout confondu
            $montant_taxe = $net_com_including_arca * ($taux / 100);
            $impot += $montant_taxe;
            
            $this->tab_taxes[] = [
                'nom' => $taxe->getNom(),
                'montant' => $montant_taxe,
                'taux' => $taux
            ];
        }

        

        $this->impotettaxetotale = $impot;

        $net_com_excluding_arca = $net_com_including_arca - $taxe_charge_courtier;

        $this->commissiontotale += $net_com_including_arca + $taxe_charge_assureur;

        $net_com_excluding_arca_sharable = $net_com_including_arca_sharable - $taxe_charge_courtier_sharable;

        $taux_retro_com = $police->getPartenaire() ? $police->getPartenaire()->getPart() : 0;

        $this->retrocommissiontotale += $net_com_excluding_arca_sharable * ($taux_retro_com / 100);
        
        $this->commissionnette += $net_com_excluding_arca;

        //$this->commissionnette += $net_com_excluding_arca - $this->retrocommissiontotale;

        

        //dd($net_com_excluding_arca);
    }

    public function getTab_Taxes()
    {
        return $this->tab_taxes;
    }

    public function getCodeMonnaie()
    {
        return $this->codemonnaie;
    }

    public function getFronting()
    {
        return $this->fronting;
    }

    public function setCodeMonnaie($valeur)
    {
        $this->codemonnaie = $valeur;
    }

    public function getPrimeTotale()
    {
        return $this->primetotale;
    }

    public function getPrimeNette()
    {
        return $this->primenette;
    }

    public function getCommissionTotale()
    {
        return $this->commissiontotale;
    }

    public function getCommissionNette()
    {
        return $this->commissionnette;
    }

    public function getRetroCommissionTotale()
    {
        return $this->retrocommissiontotale;
    }

    public function getImpotEtTaxeTotale()
    {
        return $this->impotettaxetotale;
    }


    public function setPrimeTotale($valeur)
    {
        $this->primetotale = $valeur;
    }

    public function setPrimeNette($valeur)
    {
        $this->primenette = $valeur;
    }

    public function setCommissionTotale($valeur)
    {
        $this->commissiontotale = $valeur;
    }

    public function setCommissionNette($valeur)
    {
        $this->commissionnette = $valeur;
    }

    public function setRetroCommissionTotale($valeur)
    {
        $this->retrocommissiontotale = $valeur;
    }

    public function setImpotEtTaxeTotale($valeur)
    {
        $this->impotettaxetotale = $valeur;
    }
}
