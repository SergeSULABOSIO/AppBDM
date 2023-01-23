<?php

namespace App\Agregats;


class PoliceAgregat
{
    private $codemonnaie = "";
    private $primenette = 0;
    private $primetotale = 0;
    private $commissiontotale = 0;
    private $commissionnette = 0;
    private $retrocommissiontotale = 0;
    private $impotettaxetotale = 0;

    public function getCodeMonnaie()
    {
        return $this->codemonnaie;
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
