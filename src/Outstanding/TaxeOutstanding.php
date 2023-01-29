<?php

namespace App\Outstanding;

use App\Entity\Police;

class TaxeOutstanding
{
    private ?Police $police = null;
    private $poptaxes = [];
    private $taxes = [];
    public $montantDu = 0;
    public $montantDecaisse = 0;
    public $montantSolde = 0;
    public $codeMonnaie = "...";
    public $canPay = false;

    public function __construct($police, $poptaxes, $taxes)
    {
        // $this->popcommissions = new ArrayCollection();
        if ($police !== null) {
            $this->police = $police;
        }
        if ($poptaxes !== null) {
            $this->poptaxes = $poptaxes;
        }
        if($taxes !== null){
            $this->taxes = $taxes;
        }
        $this->calculateMontantDu();
    }

    public function calculateMontantDu()
    {
        if ($this->police !== null) {
            $net_ri_com = $this->police->getRicom();
            $net_local_com = $this->police->getLocalcom();
            $net_fronting_com = $this->police->getFrontingcom();
            $net_including_arca = $net_ri_com + $net_local_com + $net_fronting_com;

            // $this->montantDu += $net_including_arca * (16 / 100);
            // $this->montantDu += $net_including_arca * (2 / 100);
            foreach ($this->taxes as $taxe) {
                $this->montantDu += $net_including_arca * ($taxe->getTaux() / 100);
            }

            if ($this->police->getMonnaie() != null) {
                $this->codeMonnaie = $this->police->getMonnaie()->getCode();
            }

            //Vérification des com encaissées
            foreach ($this->poptaxes as $poptaxe) {
                if ($poptaxe->getPolice()) {
                    if ($poptaxe->getPolice()->getId() == $this->police->getId()) {
                        $this->montantDecaisse += $poptaxe->getMontant();
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

    public function getPopTaxes()
    {
        return $this->poptaxes;
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
