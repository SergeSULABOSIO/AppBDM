<?php

namespace App\Controller;

use DateTime;
use ContactSearchType;
use PaiementTaxeSearchType;
use App\Entity\PaiementTaxe;
use App\Agregats\PopTaxeAgregat;
use App\Form\PaiementTaxeFormType;
use App\Repository\TaxeRepository;
use App\Repository\PoliceRepository;
use App\Repository\MonnaieRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PaiementTaxeRepository;
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
        $agregats = new PopTaxeAgregat();
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
            $data = $paiementTaxeRepository->findByMotCle($criteres, $agregats);
            $session->set("criteres_liste_pop_taxe", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get("criteres_liste_pop_taxe");
            if ($objCritereSession) {
                $session_police = $objCritereSession['police'];
                $session_taxe = $objCritereSession['taxe'];

                $objpolice = $session_police ? $policeRepository->find($session_police->getId()) : null;
                $objtaxe = $session_taxe ? $taxeRepository->find($session_taxe->getId()) : null;

                $data = $paiementTaxeRepository->findByMotCle($objCritereSession, $agregats);
                
                $searchPaiementTaxeForm = $this->createForm(PaiementTaxeSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'police' => $objpolice,
                    'taxe' => $objtaxe,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
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
                'paiementtaxes' => $paiementtaxes,
                'agregats' => $agregats
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





    #[Route('/deposit/{idtaxe?0}/{idpolicy?0}/{amount?0}/{idmonnaie?0}', name: 'poptaxe.deposit')]
    public function deposit(
        $idtaxe,
        $idpolicy,
        $idmonnaie,
        $amount,
        TaxeRepository $taxeRepository,
        PoliceRepository $policeRepository,
        MonnaieRepository $monnaieRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $appTitreRubrique = "";
        $adjectif = "";
        $appTitreRubrique = "Paiement des Taxes / Ajout";
        $adjectif = "ajouté";

        $poptaxe = new PaiementTaxe();
        $police = $policeRepository->find($idpolicy);
        $monnaie = $monnaieRepository->find($idmonnaie);
        $taxe = $taxeRepository->find($idtaxe);

        if ($police && $monnaie) {
            $poptaxe->setMontant($amount);
            $poptaxe->setMonnaie($monnaie);
            $poptaxe->setPolice($police);
            $poptaxe->setTaxe($taxe);            
            //dd($popcommission);

            $form = $this->createForm(PaiementTaxeFormType::class, $poptaxe);
            //vérifions le contenu de l'objet requete
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($poptaxe);
                $entityManager->flush();
                $this->addFlash('success', "Bravo ! Le paiement de " . $monnaie->getCode() . " " . $poptaxe->getMontant() . " vient d'être effectué avec succès.");
                return $this->redirectToRoute('outstanding.taxe.list');
            } else {
                return $this->render(
                    'paiementtaxe.edit.html.twig',
                    [
                        'appTitreRubrique' => $appTitreRubrique,
                        'form' => $form->createView()
                    ]
                );
            }
        } else {
            $this->addFlash('error', "La police et/ou la monnaie n'est pas définie.");
            return $this->redirectToRoute('outstanding.taxe.list');
        }
        //dd($monnaie);
        //dd("Amount: " . $amount . " idPolicy: " . $idpolicy);
    }
}
