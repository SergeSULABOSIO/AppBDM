<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use PaiementTaxeSearchType;
use App\Entity\PaiementPartenaire;
use App\Form\PaiementPartenaireFormType;
use App\Repository\PaiementPartenaireRepository;
use App\Repository\PartenaireRepository;
use App\Repository\PoliceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use PaiementPartenaireSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        PoliceRepository $policeRepository,
        PaginatorInterface $paginatorInterface
    ): Response {
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
            $data = $paiementPartenaireRepository->findByMotCle($criteres);
            $session->set("criteres_liste_pop_partenaire", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            if ($session->get("criteres_liste_pop_partenaire")) {
                $session_police = $session->get("criteres_liste_pop_partenaire")['police'];
                $session_partenaire = $session->get("criteres_liste_pop_partenaire")['partenaire'];

                $objpolice = $session_police ? $policeRepository->find($session_police->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;

                $data = $paiementPartenaireRepository->findByMotCle($session->get("criteres_liste_pop_partenaire"));

                $searchPaiementPartenaireForm = $this->createForm(PaiementPartenaireSearchType::class, [
                    'motcle' => $session->get("criteres_liste_pop_partenaire")['motcle'],
                    'police' => $objpolice,
                    'partenaire' => $objPartenaire,
                    'dateA' => $session->get("criteres_liste_pop_partenaire")['dateA'],
                    'dateB' => $session->get("criteres_liste_pop_partenaire")['dateB']
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
                'paiementpartenaires' => $paiementpartenaires
            ]
        );






        // $session = $request->getSession();
        // $appTitreRubrique = "Paiement de Partenaire";
        // $repository = $doctrine->getRepository(PaiementPartenaire::class);
        // $data = $repository->findAll();
        // $paiementpartenaires = $paginatorInterface->paginate($data, $page, $nbre);


        // return $this->render(
        //     'paiementpartenaire.list.html.twig',
        //     [
        //         'appTitreRubrique' => $appTitreRubrique,
        //         'paiementpartenaires' => $paiementpartenaires
        //     ]
        // );
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
}
