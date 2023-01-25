<?php

namespace App\Agregats;


class OutstandingCommissionAgregat
{
    private $codemonnaie = "";
    private $montant = 0;
    private $montantNet = 0;

    public function setMontant($valeur)
    {
        $this->montant = $valeur;
    }

    public function setMontantNet($valeur)
    {
        $this->montantNet = $valeur;
    }

    public function getMontant()
    {
        return $this->montant;
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
}
