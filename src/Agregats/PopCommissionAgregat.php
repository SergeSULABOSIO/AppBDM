<?php

namespace App\Agregats;


class PopCommissionAgregat
{
    private $codemonnaie = "";
    private $montantRecu = 0;
    private $montantNet = 0;
    private $tva = 0;
    private $arca = 0;


    public function setArca($valeur)
    {
        $this->arca = $valeur;
    }


    public function setTva($valeur)
    {
        $this->tva = $valeur;
    }

    public function setMontantNet($valeur)
    {
        $this->montantNet = $valeur;
    }

    public function getArca()
    {
        return $this->arca;
    }

    public function getTva()
    {
        return $this->tva;
    }

    public function getMontantNet()
    {
        return $this->montantNet;
    }

    public function getCodeMonnaie()
    {
        return $this->codemonnaie;
    }

    public function setCodeMonnaie($valeur)
    {
        $this->codemonnaie = $valeur;
    }

    public function getMontantRecu()
    {
        return $this->montantRecu;
    }

    public function setMontantRecu($valeur)
    {
        $this->montantRecu = $valeur;
    }
}
