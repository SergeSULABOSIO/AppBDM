<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Repository\PartenaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Agregats\OutstandingCommissionAgregat;
use App\Outstanding\CommissionOutstanding;
use App\Repository\OutstandingCommissionRepository;
use App\Repository\PaiementCommissionRepository;
use App\Repository\TaxeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/outstanding")]
class OutstandingCommissionController extends AbstractController
{
    #[Route('/commission/{page?1}/{nbre?20}', name: 'outstanding.commission.list')]
    public function index(
        Request $request,
        $page,
        $nbre,
        TaxeRepository $taxeRepository,
        PaiementCommissionRepository $paiementCommissionRepository,
        PoliceRepository $policeRepository,
        ProduitRepository $produitRepository,
        ClientRepository $clientRepository,
        PartenaireRepository $partenaireRepository,
        AssureurRepository $assureurRepository,
        PaginatorInterface $paginatorInterface,
        OutstandingCommissionRepository $outstandingCommissionRepository
    ): Response {
        $agregats = new OutstandingCommissionAgregat();

        $searchOutstandingForm = $this->createForm(PoliceSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);

        $searchOutstandingForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchOutstandingForm->getData();
        $taxes = $policeRepository->findAll();
        $data = [];

        if ($searchOutstandingForm->isSubmitted() && $searchOutstandingForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $policeRepository->findByMotCle($criteres, null, $taxes);
            $session->set("criteres_liste_outstanding_commission", $criteres);
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $criteres = $session->get("criteres_liste_outstanding_commission");
            if ($criteres) {
                $session_produit = $criteres['produit'] ? $criteres['produit'] : null;
                $session_partenaire = $criteres['partenaire'] ? $criteres['partenaire'] : null;
                $session_client = $criteres['client'] ? $criteres['client'] : null;
                $session_assureur = $criteres['assureur'] ? $criteres['assureur'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;

                $data = $policeRepository->findByMotCle($criteres, null, $taxes);

                $searchOutstandingForm = $this->createForm(PoliceSearchType::class, [
                    'motcle' => $criteres['motcle'],
                    'produit' => $objproduit,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $criteres['dateA'],
                    'dateB' => $criteres['dateB']
                ]);
            }else{
                $criteres['motcle'] = "";
                $criteres['dateA'] = null;
                $criteres['dateB'] = null;
                $criteres['produit'] = null;
                $criteres['client'] = null;
                $criteres['partenaire'] = null;
                $criteres['assureur'] = null;
            }
        }

        $outstandings = $outstandingCommissionRepository->findByMotCle($criteres, $agregats, $taxes);
        
        $outstandings_paginated = $paginatorInterface->paginate($outstandings, $page, $nbre);
        //dd($outstandings_paginated);

        $appTitreRubrique = "Outstanding / Commissions";
        return $this->render(
            'outstanding.commission.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchOutstandingForm->createView(),
                'outstandings' => $outstandings_paginated,
                'agregats' => $agregats
            ]
        );

        // return $this->render('outstanding_commission/index.html.twig', [
        //     'controller_name' => 'OutstandingCommissionController',
        // ]);
    }
}
