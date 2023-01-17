<?php

namespace App\Controller;

use ContactSearchType;
use PaiementTaxeSearchType;
use App\Entity\PaiementTaxe;
use App\Form\PaiementTaxeFormType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PaiementTaxeRepository;
use App\Repository\PoliceRepository;
use App\Repository\TaxeRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/poptaxe")]
class PaiementTaxeController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'poptaxe.list')]
    public function list(
        Request $request,
        $page,
        $nbre,
        PaiementTaxeRepository $paiementTaxeRepository,
        TaxeRepository $taxeRepository,
        PoliceRepository $policeRepository,
        PaginatorInterface $paginatorInterface
    ): Response {
        $searchPaiementTaxeForm = $this->createForm(PaiementTaxeSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);
        $searchPaiementTaxeForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchPaiementTaxeForm->isSubmitted() && $searchPaiementTaxeForm->isValid()) {
            $page = 1;
            $criteres = $searchPaiementTaxeForm->getData();
            //dd($criteres);
            $data = $paiementTaxeRepository->findByMotCle($criteres);
            $session->set("criteres_liste_pop_taxe", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            if ($session->get("criteres_liste_pop_taxe")) {
                $session_police = $session->get("criteres_liste_pop_taxe")['police'];
                $session_taxe = $session->get("criteres_liste_pop_taxe")['taxe'];

                $objpolice = $session_police ? $policeRepository->find($session_police->getId()) : null;
                $objtaxe = $session_taxe ? $taxeRepository->find($session_taxe->getId()) : null;

                $data = $paiementTaxeRepository->findByMotCle($session->get("criteres_liste_pop_taxe"));
                
                $searchPaiementTaxeForm = $this->createForm(PaiementTaxeSearchType::class, [
                    'motcle' => $session->get("criteres_liste_pop_taxe")['motcle'],
                    'police' => $objpolice,
                    'taxe' => $objtaxe,
                    'dateA' => $session->get("criteres_liste_pop_taxe")['dateA'],
                    'dateB' => $session->get("criteres_liste_pop_taxe")['dateB']
                ]);
            }
        }
        //dd($session->get("criteres"));
        $paiementtaxes = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Paiement de Taxe";
        return $this->render(
            'paiementtaxe.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchPaiementTaxeForm->createView(),
                'paiementtaxes' => $paiementtaxes
            ]
        );
    }





    #[Route('/details/{id<\d+>}', name: 'poptaxe.details')]
    public function detail(PaiementTaxe $paiementTaxe = null): Response
    {
        if ($paiementTaxe) {
            return $this->render('paiementtaxe.details.html.twig', ['paiementtaxe' => $paiementTaxe]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('poptaxe.list');
        }
    }





    #[Route('/edit/{id?0}', name: 'poptaxe.edit')]
    public function edit(PaiementTaxe $poptaxe = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($poptaxe == null) {
            $appTitreRubrique = "Paiement de Taxe / Ajout";
            $adjectif = "ajoutée";
            $poptaxe = new PaiementTaxe();
        } else {
            $appTitreRubrique = "Paiement de Taxe / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(PaiementTaxeFormType::class, $poptaxe);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($poptaxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poptaxe->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('poptaxe.list');
        } else {

            return $this->render(
                'paiementtaxe.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'poptaxe.delete')]
    public function delete(PaiementTaxe $poptaxe = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($poptaxe != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($poptaxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poptaxe->getMontant() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('poptaxe.list');
    }
}
