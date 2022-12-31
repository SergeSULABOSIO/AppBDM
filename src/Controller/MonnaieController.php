<?php

namespace App\Controller;

use App\Entity\Monnaie;
use App\Form\MonnaieFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/monnaie")]
class MonnaieController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'monnaie.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Monnaie";
        $repository = $doctrine->getRepository(Monnaie::class);
        $data = $repository->findAll();
        $monnaies = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'monnaie.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'monnaies' => $monnaies
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'monnaie.details')]
    public function detail(Monnaie $monnaie = null): Response
    {
        if ($monnaie) {
            return $this->render('monnaie.details.html.twig', ['monnaie' => $monnaie]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('monnaie.list');
        }
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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($monnaie);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $monnaie->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('monnaie.list');
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
