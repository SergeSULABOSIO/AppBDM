<?php

namespace App\Controller;

use App\Entity\Secteur;
use App\Form\SecteurFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/secteur")]
class SecteurController extends AbstractController
{

    #[Route('/', name: 'secteur.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Secteurs";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'secteur/secteur.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'secteur.edit')]
    public function edit(Secteur $secteur = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if($secteur == null){
            $appTitreRubrique = "Secteur / Ajout";
            $adjectif = "ajoutée";
            $secteur = new Secteur();
        }else{
            $appTitreRubrique = "Secteur / Edition";
            $adjectif = "modifiée";
        }
        
        $form = $this->createForm(SecteurFormType::class, $secteur);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($secteur);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $secteur->getNom() . " vient d'être ". $adjectif ." avec succès.");
            return $this->redirectToRoute('secteur.edit');
        } else {

            return $this->render(
                'secteur/secteur.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'secteur.delete')]
    public function delete(Secteur $secteur = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if($secteur != null){
            $entityManager = $doctrine->getManager();
            $entityManager->remove($secteur);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $secteur->getNom() . " vient d'être supprimée avec succès.");
        }else{
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('secteur.list');
    }
}
