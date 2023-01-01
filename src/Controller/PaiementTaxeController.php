<?php

namespace App\Controller;

use App\Entity\PaiementTaxe;
use App\Form\PaiementTaxeFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/poptaxe")]
class PaiementTaxeController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'poptaxe.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Paiement de Taxe";
        $repository = $doctrine->getRepository(PaiementTaxe::class);
        $data = $repository->findAll();
        $paiementtaxes = $paginatorInterface->paginate($data, $page, $nbre);

        return $this->render(
            'paiementtaxe.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'paiementtaxes' => $paiementtaxes
            ]
        );
    }





    #[Route('/details/{id<\d+>}', name: 'poptaxe.details')]
    public function detail(PaiementTaxe $paiementTaxe = null): Response
    {
        if ($paiementTaxe) {
            return $this->render('paiementtaxe.details.html.twig', ['paiementtaxe' => $paiementTaxe]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('poptaxe.list');
        }
    }





    #[Route('/edit/{id?0}', name: 'poptaxe.edit')]
    public function edit(PaiementTaxe $poptaxe = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($poptaxe == null) {
            $appTitreRubrique = "Paiement de Taxe / Ajout";
            $adjectif = "ajoutée";
            $poptaxe = new PaiementTaxe();
        } else {
            $appTitreRubrique = "Paiement de Taxe / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(PaiementTaxeFormType::class, $poptaxe);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($poptaxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poptaxe->getMontant() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('poptaxe.list');
        } else {

            return $this->render(
                'paiementtaxe.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'poptaxe.delete')]
    public function delete(PaiementTaxe $poptaxe = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($poptaxe != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($poptaxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $poptaxe->getMontant() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('poptaxe.list');
    }
}
