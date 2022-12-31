<?php

namespace App\Controller;

use App\Entity\Police;
use App\Entity\Entreprise;
use App\Form\PoliceFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/police")]
class PoliceController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'police.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Police";
        $repository = $doctrine->getRepository(Police::class);
        $data = $repository->findAll();
        $polices = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'police.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'polices' => $polices
            ]
        );
    }



    #[Route('/details/{id<\d+>}', name: 'police.details')]
    public function detail(Police $police = null): Response
    {
        if ($police) {
            return $this->render('police.details.html.twig', ['police' => $police]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('police.list');
        }
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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($police);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $police->getReference() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('police.list');
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
