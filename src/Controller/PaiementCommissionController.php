<?php

namespace App\Controller;

use App\Entity\PaiementCommission;
use App\Form\PaiementCommissionFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/popcommission")]
class PaiementCommissionController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'popcommission.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Paiement de Commission";
        $repository = $doctrine->getRepository(PaiementCommission::class);
        $data = $repository->findAll();
        $paiementcommissions = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'paiementcommission.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'paiementcommissions' => $paiementcommissions
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'popcommission.details')]
    public function detail(PaiementCommission $paiementCommission = null): Response
    {
        if ($paiementCommission) {
            return $this->render('paiementCommission.details.html.twig', ['paiementcommission' => $paiementCommission]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('popcommission.list');
        }
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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($popcommission);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $popcommission->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('popcommission.list');
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
