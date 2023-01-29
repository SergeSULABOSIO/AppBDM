<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Outstanding\TaxeOutstanding;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Agregats\OutstandingTaxeAgregat;
use App\Outstanding\RetrocomOutstanding;
use App\Repository\PartenaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Agregats\OutstandingCommissionAgregat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaiementPartenaireRepository;
use App\Agregats\OutstandingRetroCommissionAgregat;
use App\Repository\PaiementTaxeRepository;
use App\Repository\TaxeRepository;
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

        $searchOutstandingForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchOutstandingForm->getData();

        $data = [];

        if ($searchOutstandingForm->isSubmitted() && $searchOutstandingForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $policeRepository->findByMotCle($criteres, null);
            $session->set($nomSession, $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get($nomSession);
            if ($objCritereSession) {
                $session_produit = $objCritereSession['produit'] ? $objCritereSession['produit'] : null;
                $session_partenaire = $objCritereSession['partenaire'] ? $objCritereSession['partenaire'] : null;
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $session_assureur = $objCritereSession['assureur'] ? $objCritereSession['assureur'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;

                $data = $policeRepository->findByMotCle($objCritereSession, null);

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

            // dd($taxes);

            $taxeOustanding = new TaxeOutstanding($police, $data_popTaxes, $taxes);

            //dd($commOustanding);

            if ($taxeOustanding->montantSolde != 0) {
                $agreg_montant += $taxeOustanding->montantSolde;
                $agreg_codeMonnaie = $taxeOustanding->codeMonnaie;
                $outstandings[] = $taxeOustanding;
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
