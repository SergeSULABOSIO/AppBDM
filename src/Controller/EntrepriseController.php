<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/entreprise")]
class EntrepriseController extends AbstractController
{

    #[Route('/', name: 'entreprise.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Entreprises";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'entreprise.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'entreprise.edit')]
    public function edit(Entreprise $entreprise = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($entreprise == null) {
            $appTitreRubrique = "Entreprise / Ajout";
            $adjectif = "ajoutée";
            $entreprise = new Entreprise();
        } else {
            $appTitreRubrique = "Entreprise / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(EntrepriseFormType::class, $entreprise);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $entreprise->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('entreprise.edit');
        } else {

            return $this->render(
                'entreprise.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'entreprise.delete')]
    public function delete(Entreprise $entreprise = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($entreprise != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($entreprise);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $entreprise->getNom() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('entreprise.list');
    }
}
