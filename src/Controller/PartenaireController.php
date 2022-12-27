<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/partenaire")]
class PartenaireController extends AbstractController
{

    #[Route('/', name: 'partenaire.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Partenaire";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'partenaire.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
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
            return $this->redirectToRoute('partenaire.edit');
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
