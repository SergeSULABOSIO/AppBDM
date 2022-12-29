<?php

namespace App\Controller;

use App\Entity\Assureur;
use App\Form\AssureurFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Assureur";

        $repository = $doctrine->getRepository(Assureur::class);
        //$assureurs = $repository->findAll();
        $assureurs = $repository->findBy([], ['id' => 'DESC'], $nbre, ($page - 1) * $nbre);

        return $this->render(
            'assureur.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'assureurs' => $assureurs
            ]
        );
    }
}
