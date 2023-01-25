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
        PoliceRepository $policeRepository,
        ProduitRepository $produitRepository,
        ClientRepository $clientRepository,
        PartenaireRepository $partenaireRepository,
        AssureurRepository $assureurRepository,
        PaginatorInterface $paginatorInterface
    ): Response {
        $agregats = new OutstandingCommissionAgregat();

        $searchOutstandingForm = $this->createForm(PoliceSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);

        $searchOutstandingForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchOutstandingForm->getData();

        $data = [];
        if ($searchOutstandingForm->isSubmitted() && $searchOutstandingForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $policeRepository->findByMotCle($criteres, null);
            $session->set("criteres_liste_outstanding_commission", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get("criteres_liste_outstanding_commission");
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
        $agreg_montant_net = 0;
        foreach ($data as $police) {
            $commOustanding = new CommissionOutstanding($police, null);
            $outstandings[] = $commOustanding;

            $agreg_montant += $commOustanding->montantDu;
            $agreg_montant_net += ($commOustanding->montantDu) / 1.16;
            $agreg_codeMonnaie = $commOustanding->codeMonnaie;
        }
        $agregats->setCodeMonnaie($agreg_codeMonnaie);
        $agregats->setMontant($agreg_montant);
        $agregats->setMontantNet($agreg_montant_net);
        //dd($outstandings);


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
