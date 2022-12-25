<?php

namespace App\Controller;

use App\Entity\PaiementPartenaire;
use App\Form\PaiementPartenaireFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/poppartenaire")]
class PaiementPartenaireController extends AbstractController
{

    #[Route('/', name: 'poppartenaire.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Paiement de Partenaire";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'paiementpartenaire.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
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
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($poppartenaire);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poppartenaire->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('poppartenaire.edit');
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
