<?php

namespace App\Controller;

use App\Entity\Automobile;
use App\Form\AutomobileFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/automobile")]
class AutomobileController extends AbstractController
{

    #[Route('/', name: 'automobile.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Automobile";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'automobile.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'automobile.edit')]
    public function edit(Automobile $automobile = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($automobile == null) {
            $appTitreRubrique = "Automobile / Ajout";
            $adjectif = "ajouté";
            $automobile = new Automobile();
        } else {
            $appTitreRubrique = "Automobile / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(AutomobileFormType::class, $automobile);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($automobile);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $automobile->getMarque() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('automobile.edit');
        } else {

            return $this->render(
                'automobile.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'automobile.delete')]
    public function delete(Automobile $automobile = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($automobile != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($automobile);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $automobile->getMarque() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('automobile.list');
    }
}
