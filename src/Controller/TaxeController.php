<?php

namespace App\Controller;

use App\Entity\Taxe;
use App\Form\TaxeFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/taxe")]
class TaxeController extends AbstractController
{

    #[Route('/', name: 'taxe.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Taxe";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'taxe.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'taxe.edit')]
    public function edit(Taxe $taxe = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($taxe == null) {
            $appTitreRubrique = "Taxe / Ajout";
            $adjectif = "ajoutée";
            $taxe = new Taxe();
        } else {
            $appTitreRubrique = "Taxe / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(TaxeFormType::class, $taxe);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($taxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $taxe->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('taxe.edit');
        } else {

            return $this->render(
                'taxe.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'taxe.delete')]
    public function delete(Taxe $taxe = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($taxe != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($taxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $taxe->getNom() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('taxe.list');
    }
}
