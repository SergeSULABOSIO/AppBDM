<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use PaiementTaxeSearchType;
use PaiementPartenaireSearchType;
use App\Entity\PaiementPartenaire;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\MonnaieRepository;
use App\Agregats\PopPartenaireAgregat;
use App\Form\PaiementPartenaireFormType;
use App\Repository\PartenaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaiementPartenaireRepository;
use App\Repository\TaxeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/poppartenaire")]
class PaiementPartenaireController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'poppartenaire.list')]
    public function list(
        Request $request,
        $page,
        $nbre,
        PaiementPartenaireRepository $paiementPartenaireRepository,
        PartenaireRepository $partenaireRepository,
        ClientRepository $clientRepository,
        PoliceRepository $policeRepository,
        PaginatorInterface $paginatorInterface
    ): Response {
        $agregats = new PopPartenaireAgregat();
        $searchPaiementPartenaireForm = $this->createForm(PaiementPartenaireSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);
        $searchPaiementPartenaireForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchPaiementPartenaireForm->isSubmitted() && $searchPaiementPartenaireForm->isValid()) {
            $page = 1;
            $criteres = $searchPaiementPartenaireForm->getData();
            //dd($criteres);
            $data = $paiementPartenaireRepository->findByMotCle($criteres, $agregats);
            $session->set("criteres_liste_pop_partenaire", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get("criteres_liste_pop_partenaire");
            if ($objCritereSession) {
                $session_police = $objCritereSession['police']?$objCritereSession['police']:null;
                $session_client = $objCritereSession['client']?$objCritereSession['client']:null;
                $session_partenaire = $objCritereSession['partenaire']?$objCritereSession['partenaire']:null;

                $objpolice = $session_police ? $policeRepository->find($session_police->getId()) : null;
                $objclient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;

                $data = $paiementPartenaireRepository->findByMotCle($objCritereSession, $agregats);

                $searchPaiementPartenaireForm = $this->createForm(PaiementPartenaireSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'police' => $objpolice,
                    'client' => $objclient,
                    'partenaire' => $objPartenaire,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
                ]);
            }
        }
        //dd($session->get("criteres"));
        $paiementpartenaires = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Paiement du Partenaire";
        return $this->render(
            'paiementpartenaire.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchPaiementPartenaireForm->createView(),
                'paiementpartenaires' => $paiementpartenaires,
                'agregats' => $agregats
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'poppartenaire.details')]
    public function detail(PaiementPartenaire $paiementpartenaire = null): Response
    {
        if ($paiementpartenaire) {
            return $this->render('paiementpartenaire.details.html.twig', ['paiementpartenaire' => $paiementpartenaire]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('poppartenaire.list');
        }
    }






    #[Route('/edit/{id?0}', name: 'poppartenaire.edit')]
    public function edit(PaiementPartenaire $poppartenaire = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($poppartenaire == null) {
            $appTitreRubrique = "Paiement de Partenaire / Ajout";
            $adjectif = "ajouté";
            $poppartenaire = new PaiementPartenaire();
        } else {
            $appTitreRubrique = "Paiement de Partenaire / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(PaiementPartenaireFormType::class, $poppartenaire);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($poppartenaire);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poppartenaire->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('poppartenaire.list');
        } else {

            return $this->render(
                'paiementpartenaire.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'poppartenaire.delete')]
    public function delete(PaiementPartenaire $poppartenaire = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($poppartenaire != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($poppartenaire);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poppartenaire->getMontant() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('poppartenaire.list');
    }


    #[Route('/deposit/{idpolicy?0}/{amount?0}/{idmonnaie?0}', name: 'popretrocommission.deposit')]
    public function deposit(
        $idpolicy,
        $idmonnaie,
        $amount,
        PoliceRepository $policeRepository,
        PartenaireRepository $partenaireRepository,
        MonnaieRepository $monnaieRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $appTitreRubrique = "";
        $appTitreRubrique = "Paiement de la RetroCommission / Ajout";
        $adjectif = "ajouté";

        $popretrocommission = new PaiementPartenaire();
        $police = $policeRepository->find($idpolicy);
        $partenaire = $partenaireRepository->find($police->getPartenaire());
        $monnaie = $monnaieRepository->find($idmonnaie);

        if ($police && $monnaie) {
            $popretrocommission->setMontant($amount);
            $popretrocommission->setMonnaie($monnaie);
            $popretrocommission->setPolice($police);
            $popretrocommission->setPartenaire($partenaire);
            //$popretrocommission->setDescription("Paiement Commission de Courtage / Police: " . $police->getReference() . " / Client: " . $police->getClient()->getNom());

            //dd($popcommission);

            $form = $this->createForm(PaiementPartenaireFormType::class, $popretrocommission);
            //vérifions le contenu de l'objet requete
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($popretrocommission);
                $entityManager->flush();
                $this->addFlash('success', "Bravo ! Le décaissement de " . $monnaie->getCode() . " " . $popretrocommission->getMontant() . " vient d'être effectué avec succès.");
                return $this->redirectToRoute('outstanding.retrocommission.list');
            } else {
                return $this->render(
                    'paiementpartenaire.edit.html.twig',
                    [
                        'appTitreRubrique' => $appTitreRubrique,
                        'form' => $form->createView()
                    ]
                );
            }
        } else {
            $this->addFlash('error', "La police et/ou la monnaie n'est pas définie.");
            return $this->redirectToRoute('outstanding.retrocommission.list');
        }
    }
}
