<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFromType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/produit")]
class ProduitController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'produit.list')]
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Produit";
        $repository = $doctrine->getRepository(Produit::class);
        $data = $repository->findAll();
        $produits = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'produit.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'produits' => $produits
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'produit.details')]
    public function detail(Produit $produit = null): Response
    {
        if ($produit) {
            return $this->render('produit.details.html.twig', ['produit' => $produit]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('produit.list');
        }
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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $produit->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('produit.list');
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
