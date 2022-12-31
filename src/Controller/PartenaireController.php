<?php

namespace App\Controller;

use App\Entity\Automobile;
use App\Entity\Partenaire;
use App\Form\PartenaireFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/partenaire")]
class PartenaireController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'partenaire.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Partenaire";
        $repository = $doctrine->getRepository(Partenaire::class);
        $data = $repository->findAll();
        $partenaires = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'partenaire.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'partenaires' => $partenaires
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'partenaire.details')]
    public function detail(Partenaire $partenaire = null): Response
    {
        if ($partenaire) {
            return $this->render('partenaire.details.html.twig', ['partenaire' => $partenaire]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('partenaire.list');
        }
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
            return $this->redirectToRoute('partenaire.list');
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
