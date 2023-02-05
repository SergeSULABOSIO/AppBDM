<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Repository\TaxeRepository;
use App\Outstanding\TaxeOutstanding;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Agregats\OutstandingTaxeAgregat;
use App\Outstanding\RetrocomOutstanding;
use App\Repository\PartenaireRepository;
use App\Outstanding\CommissionOutstanding;
use App\Repository\PaiementTaxeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Agregats\OutstandingCommissionAgregat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaiementCommissionRepository;
use App\Repository\PaiementPartenaireRepository;
use App\Agregats\OutstandingRetroCommissionAgregat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/outstanding")]
class OutstandingTaxeController extends AbstractController
{
    #[Route('/taxe/{page?1}/{nbre?20}', name: 'outstanding.taxe.list')]
    public function index(
        Request $request,
        $page,
        $nbre,
        PaiementTaxeRepository $paiementTaxeRepository,
        PaiementCommissionRepository $paiementCommissionRepository,
        TaxeRepository $taxeRepository,
        PoliceRepository $policeRepository,
        ProduitRepository $produitRepository,
        ClientRepository $clientRepository,
        PartenaireRepository $partenaireRepository,
        AssureurRepository $assureurRepository,
        PaginatorInterface $paginatorInterface
    ): Response {
        $agregats = new OutstandingTaxeAgregat();
        $nomSession = "criteres_liste_outstanding_taxe";
        $searchOutstandingForm = $this->createForm(PoliceSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);

        $taxes = $taxeRepository->findByMotCle([
            'motcle' => ""
        ]);

        $objTaxeSelected = null;

        $searchOutstandingForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchOutstandingForm->getData();
        $data = [];

        if ($searchOutstandingForm->isSubmitted() && $searchOutstandingForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $policeRepository->findByMotCle($criteres, null, $taxes);
            $session->set($nomSession, $criteres);
            $objTaxeSelected = $criteres['taxe'];
            //dd($objTaxeSelected);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get($nomSession);
            if ($objCritereSession) {
                $session_produit = $objCritereSession['produit'] ? $objCritereSession['produit'] : null;
                $session_partenaire = $objCritereSession['partenaire'] ? $objCritereSession['partenaire'] : null;
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $session_assureur = $objCritereSession['assureur'] ? $objCritereSession['assureur'] : null;
                $session_taxe = $objCritereSession['taxe'] ? $objCritereSession['taxe'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;
                $objTaxeSelected = $session_taxe ? $taxeRepository->find($session_taxe->getId()) : null;

                $data = $policeRepository->findByMotCle($objCritereSession, null , $taxes);

                $searchOutstandingForm = $this->createForm(PoliceSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'produit' => $objproduit,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
                ]);
            }
        }

        //dd($data);

        $outstandings = [];

        //$polices = $paginatorInterface->paginate($data, $page, $nbre);
        $agreg_codeMonnaie = "...";
        $agreg_montant = 0;
        foreach ($data as $police) {
            //On va vérifier aussi les paiements possibles
            $data_popTaxes = $paiementTaxeRepository->findByMotCle([
                'dateA' => "",
                'dateB' => "",
                'motcle' => "",
                'police' => $police,
                'taxe' => null
            ], null);

            //dd($taxes);

            foreach ($taxes as $taxe) {
                $taxeOustanding = null;
                if($objTaxeSelected){
                    if($objTaxeSelected == $taxe){
                        $taxeOustanding = new TaxeOutstanding($police, $data_popTaxes, $taxe);
                    }else{
                        continue;
                    }
                }else{
                    $taxeOustanding = new TaxeOutstanding($police, $data_popTaxes, $taxe);
                }

                //dd($taxeOustanding);
                if ($taxeOustanding->montantSolde != 0) {
                    $agreg_montant += $taxeOustanding->montantSolde;
                    $agreg_codeMonnaie = $taxeOustanding->codeMonnaie;
                    //On vérifie si nous avons déjà encaissé toutes les commissions relatives à cette police
                    $data_paiementsCommissions = $paiementCommissionRepository->findByMotCle([
                        'dateA' => "",
                        'dateB' => "",
                        'motcle' => "",
                        'police' => $police,
                        'assureur' => null,
                        'client' => $police->getClient(),
                        'partenaire' => $police->getPartenaire()
                    ], null);
                    $commOustanding = new CommissionOutstanding($police, $data_paiementsCommissions);
                    //dd($commOustanding);
                    //Sur le twig on ne pouura etre en mesure de payer que les Outstanding retrocom pour lesquelles nous avons déjà enciassé 100% de commission de courtage!!!
                    if ($commOustanding->montantSolde == 0) {
                        $taxeOustanding->setCanPay(true);
                    } else {
                        $taxeOustanding->setCanPay(false);
                    }
                    $outstandings[] = $taxeOustanding;
                }
            }
        }
        $agregats->setCodeMonnaie($agreg_codeMonnaie);
        $agregats->setMontant($agreg_montant);
        //dd($outstandings);


        $outstandings_paginated = $paginatorInterface->paginate($outstandings, $page, $nbre);
        //dd($outstandings_paginated);

        $appTitreRubrique = "Outstanding / Taxes";
        return $this->render(
            'outstanding.taxe.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchOutstandingForm->createView(),
                'outstandings' => $outstandings_paginated,
                'agregats' => $agregats
            ]
        );
    }
}
