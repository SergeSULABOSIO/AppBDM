<?php

namespace App\Outstanding;

use App\Entity\Police;
use Symfony\Component\Validator\Constraints\Collection;

class CommissionOutstanding
{
    public ?Police $police = null;
    public $popcommissions = [];
    public $montantDu;
    public $montantEncaisse;
    public $montantSolde;
    public $codeMonnaie = "...";

    public function __construct($police, $popcommissions)
    {
        // $this->popcommissions = new ArrayCollection();
        if ($police !== null) {
            $this->police = $police;
        }
        if ($popcommissions !== null) {
            $this->popcommissions = $popcommissions;
        }
        $this->calculateMontantDu();
    }

    public function calculateMontantDu()
    {
        if ($this->police !== null) {
            $net_ri_com = $this->police->getRicom();
            $net_local_com = $this->police->getLocalcom();
            $net_fronting_com = $this->police->getFrontingcom();
            $net = $net_ri_com + $net_local_com + $net_fronting_com;
            $tva = $net * (16 / 100);
            $this->montantDu = $net + $tva;

            if ($this->police->getMonnaie() != null) {
                $this->codeMonnaie = $this->police->getMonnaie()->getCode();
            }

            //Vérification des com encaissées
            foreach ($this->popcommissions as $popcom) {
                if ($popcom->getPolice()) {
                    if ($popcom->getPolice()->getId() == $this->police->getId()) {
                        $this->montantEncaisse += $popcom->getMontant();
                    }
                }
            }
            $this->montantSolde = $this->montantDu - $this->montantEncaisse;
        }
    }

    public function getPolice():Police
    {
        return $this->police;
    }

    public function getPopCommissions()
    {
        return $this->popcommissions;
    }

    public function getSoldeDue(){
        return $this->montantSolde;
    }
}
