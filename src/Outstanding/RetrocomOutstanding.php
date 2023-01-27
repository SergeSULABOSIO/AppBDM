<?php

namespace App\Outstanding;

use App\Entity\Police;

class RetrocomOutstanding
{
    private ?Police $police = null;
    private $poppartenaires = [];
    public $montantDu;
    public $montantDecaisse;
    public $montantSolde;
    public $codeMonnaie = "...";

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
                $net_ri_com = $this->police->getLocalcom();
            }
            if($this->police->isCansharefrontingcom()){
                $net_ri_com = $this->police->getFrontingcom();
            }


            $net_including_arca = $net_ri_com + $net_local_com + $net_fronting_com;
            $net = $net_including_arca / 1.02;
            

            //si le partenaire était défini
            $this->montantDu = 0;
            if($this->police->getPartenaire()){
                $part = ($this->police->getPartenaire()->getPart()) / 100;
                $this->montantDu = $net * $part;
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
}
