<?php

namespace App\Controller;

use App\Entity\PaiementCommission;
use App\Form\PaiementCommissionFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/popcommission")]
class PaiementCommissionController extends AbstractController
{

    #[Route('/', name: 'popcommission.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Paiement de Commission";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'paiementcommission.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'popcommission.edit')]
    public function edit(PaiementCommission $popcommission = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($popcommission == null) {
            $appTitreRubrique = "Paiement de Commission / Ajout";
            $adjectif = "ajouté";
            $popcommission = new PaiementCommission();
        } else {
            $appTitreRubrique = "Paiement de Commission / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(PaiementCommissionFormType::class, $popcommission);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($popcommission);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $popcommission->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('popcommission.edit');
        } else {

            return $this->render(
                'paiementcommission.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'popcommission.delete')]
    public function delete(PaiementCommission $popcommission = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($popcommission != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($popcommission);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $popcommission->getMontant() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('popcommission.list');
    }
}
