<?php

namespace App\Controller;

use App\Entity\Police;
use App\Form\PoliceFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/police")]
class PoliceController extends AbstractController
{

    #[Route('/', name: 'police.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Police";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'police.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'police.edit')]
    public function edit(Police $police = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($police == null) {
            $appTitreRubrique = "Police / Ajout";
            $adjectif = "ajoutée";
            $police = new Police();
        } else {
            $appTitreRubrique = "Police / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(PoliceFormType::class, $police);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($police);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $police->getReference() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('police.edit');
        } else {

            return $this->render(
                'police.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'police.delete')]
    public function delete(Police $police = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($police != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($police);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $police->getReference() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('police.list');
    }
}
