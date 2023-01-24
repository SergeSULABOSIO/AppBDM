<?php

namespace App\Agregats;


class PopTaxeAgregat
{
    private $codemonnaie = "";
    private $montant = 0;

    public function setMontant($valeur)
    {
        $this->montant = $valeur;
    }

    public function getMontant()
    {
        return $this->montant;
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
