<?php

namespace App\Controller;

use PartenaireSearchType;
use App\Entity\Partenaire;
use App\Form\PartenaireFormType;
use App\Repository\PartenaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/partenaire")]
class PartenaireController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'partenaire.list')]
    public function list(Request $request, $page, $nbre, PartenaireRepository $partenaireRepository, PaginatorInterface $paginatorInterface): Response
    {
        $searchPartenaireForm = $this->createForm(PartenaireSearchType::class);
        $searchPartenaireForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchPartenaireForm->isSubmitted() && $searchPartenaireForm->isValid()) {
            $page = 1;
            $criteres = $searchPartenaireForm->getData();
            $data = $partenaireRepository->findByMotCle($criteres);
            $session->set("criteres_liste_partenaire", $criteres);
        } else {
            if ($session->has("criteres_liste_partenaire")) {
                $data = $partenaireRepository->findByMotCle($session->get("criteres_liste_partenaire"));
                $criteres = new PartenaireSearchType();
                $searchPartenaireForm = $this->createForm(PartenaireSearchType::class, [
                    'motcle' => $session->get("criteres_liste_partenaire")['motcle']
                ]);
            } else {
                $data = $partenaireRepository->findAll();
            }
        }
        //dd($session->get("criteres"));
        $partenaires = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Partenaire";
        return $this->render(
            'partenaire.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchPartenaireForm->createView(),
                'partenaires' => $partenaires
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'partenaire.details')]
    public function detail(Partenaire $partenaire = null): Response
    {
        if ($partenaire) {
            return $this->render('partenaire.details.html.twig', ['partenaire' => $partenaire]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('partenaire.list');
        }
    }



    #[Route('/edit/{id?0}', name: 'partenaire.edit')]
    public function edit(Partenaire $partenaire = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($partenaire == null) {
            $appTitreRubrique = "Partenaire / Ajout";
            $adjectif = "ajoutée";
            $partenaire = new Partenaire();
        } else {
            $appTitreRubrique = "Partenaire / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(PartenaireFormType::class, $partenaire);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $partenaire->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('partenaire.list');
        } else {

            return $this->render(
                'partenaire.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'partenaire.delete')]
    public function delete(Partenaire $partenaire = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($partenaire != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $partenaire->getNom() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('partenaire.list');
    }
}
