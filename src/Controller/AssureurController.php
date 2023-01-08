<?php

namespace App\Controller;

use App\Entity\Assureur;
use App\Form\AssureurFormType;
use App\Repository\AssureurRepository;
use AssureurSearchType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/assureur")]
class AssureurController extends AbstractController
{


    #[Route('/edit/{id?0}', name: 'assureur.edit')]
    public function edit(Assureur $assureur = null, ManagerRegistry $doctrine, Request $request): Response
    {
        $appTitreRubrique = "";
        $adjectif = "";
        if ($assureur == null) {
            $appTitreRubrique = "Assureur / Ajout";
            $adjectif = "ajouté";
            $assureur = new Assureur();
        } else {
            $appTitreRubrique = "Assureur / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(AssureurFormType::class, $assureur);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($assureur);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $assureur->getNom() . " vient d'être " . $adjectif . " avec succès.");
            //return $this->redirectToRoute('assureur.edit');
            return $this->redirectToRoute('assureur.list');
        } else {

            return $this->render(
                'assureur.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'assureur.delete')]
    public function delete(Assureur $assureur = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($assureur != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($assureur);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $assureur->getNom() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('assureur.list');
    }






    #[Route('/details/{id<\d+>}', name: 'assureur.details')]
    public function detail(Assureur $assureur = null): Response
    {
        if ($assureur) {
            return $this->render('assureur.details.html.twig', ['assureur' => $assureur]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('assureur.list');
        }
    }






    #[Route('/list/{page?1}/{nbre?20}', name: 'assureur.list')]
    public function list(Request $request, $page, $nbre, AssureurRepository $assureurRepository, PaginatorInterface $paginatorInterface): Response
    {
        $searchAssureursForm = $this->createForm(AssureurSearchType::class);
        $searchAssureursForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchAssureursForm->isSubmitted() && $searchAssureursForm->isValid()) {
            $page = 1;
            $criteres = $searchAssureursForm->getData();
            $data = $assureurRepository->findByMotCle($criteres);
            $session->set("criteres_liste_assureur", $criteres);
        } else {
            if ($session->has("criteres_liste_assureur")) {
                $data = $assureurRepository->findByMotCle($session->get("criteres_liste_assureur"));
                $criteres = new AssureurSearchType();
                $searchAssureursForm = $this->createForm(AssureurSearchType::class, [
                    'motcle' => $session->get("criteres_liste_assureur")['motcle']
                ]);
            } else {
                $data = $assureurRepository->findAll();
            }
        }
        //dd($session->get("criteres"));
        $assureurs = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Assureur";
        return $this->render(
            'assureur.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchAssureursForm->createView(),
                'assureurs' => $assureurs
            ]
        );
    }
}
