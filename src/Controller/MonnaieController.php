<?php

namespace App\Controller;

use App\Entity\Monnaie;
use App\Form\MonnaieFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/monnaie")]
class MonnaieController extends AbstractController
{

    #[Route('/', name: 'monnaie.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Monnaie";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'monnaie.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'monnaie.edit')]
    public function edit(Monnaie $monnaie = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($monnaie == null) {
            $appTitreRubrique = "Monnaie / Ajout";
            $adjectif = "ajoutée";
            $monnaie = new Monnaie();
        } else {
            $appTitreRubrique = "Monnaie / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(MonnaieFormType::class, $monnaie);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($monnaie);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $monnaie->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('monnaie.edit');
        } else {

            return $this->render(
                'monnaie.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'monnaie.delete')]
    public function delete(Monnaie $monnaie = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($monnaie != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($monnaie);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $monnaie->getNom() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('monnaie.list');
    }
}
