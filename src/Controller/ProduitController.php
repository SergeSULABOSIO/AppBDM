<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFromType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/produit")]
class ProduitController extends AbstractController
{

    #[Route('/', name: 'produit.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Produit";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'produit.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'produit.edit')]
    public function edit(Produit $produit = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($produit == null) {
            $appTitreRubrique = "Produit / Ajout";
            $adjectif = "ajoutée";
            $produit = new Produit();
        } else {
            $appTitreRubrique = "Produit / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(ProduitFromType::class, $produit);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $produit->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('produit.edit');
        } else {

            return $this->render(
                'produit.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'produit.delete')]
    public function delete(Produit $produit = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($produit != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $produit->getNom() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('produit.list');
    }
}
