<?php

namespace App\Controller;

use App\Entity\Automobile;
use App\Entity\Entreprise;
use App\Form\EntrepriseFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/entreprise")]
class EntrepriseController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'entreprise.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Entreprise";
        $repository = $doctrine->getRepository(Entreprise::class);
        $data = $repository->findAll();
        $entreprises = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'entreprise.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'entreprises' => $entreprises
            ]
        );
    }



    #[Route('/details/{id<\d+>}', name: 'entreprise.details')]
    public function detail(Entreprise $entreprise = null): Response
    {
        if ($entreprise) {
            return $this->render('entreprise.details.html.twig', ['entreprise' => $entreprise]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('entreprise.list');
        }
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
            return $this->redirectToRoute('entreprise.list');
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
