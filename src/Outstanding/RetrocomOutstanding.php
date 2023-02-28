<?php

namespace App\Outstanding;

use App\Entity\Police;

class RetrocomOutstanding
{
    private ?Police $police = null;
    private $poppartenaires = [];
    public $montantNetPartageable = 0;
    public $montantDu = 0;
    public $montantDecaisse = 0;
    public $montantSolde = 0;
    public $codeMonnaie = "...";
    public $canPay = false;

    public function __construct($police, $poppartenaires)
    {
        // $this->popcommissions = new ArrayCollection();
        if ($police !== null) {
            $this->police = $police;
        }
        if ($poppartenaires !== null) {
            $this->poppartenaires = $poppartenaires;
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

            $arca = $net_including_arca * (2 / 100);

            $this->montantNetPartageable = $net_including_arca - $arca;
            

            //si le partenaire était défini
            $this->montantDu = 0;
            if($this->police->getPartenaire()){
                $part = ($this->police->getPartenaire()->getPart()) / 100;
                $this->montantDu = $this->montantNetPartageable * $part;
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
