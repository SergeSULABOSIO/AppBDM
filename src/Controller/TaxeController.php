<?php

namespace App\Controller;

use TaxeSearchType;
use App\Entity\Taxe;
use App\Form\TaxeFormType;
use App\Repository\TaxeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/taxe")]
class TaxeController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'taxe.list')]
    public function list(Request $request, $page, $nbre, TaxeRepository $taxeRepository, PaginatorInterface $paginatorInterface): Response
    {

        $searchTaxeForm = $this->createForm(TaxeSearchType::class);
        $searchTaxeForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchTaxeForm->isSubmitted() && $searchTaxeForm->isValid()) {
            $page = 1;
            $criteres = $searchTaxeForm->getData();
            $data = $taxeRepository->findByMotCle($criteres);
            $session->set("criteres_liste_taxe", $criteres);
        } else {
            if ($session->has("criteres_liste_taxe")) {
                $data = $taxeRepository->findByMotCle($session->get("criteres_liste_taxe"));
                $searchTaxeForm = $this->createForm(TaxeSearchType::class, [
                    'motcle' => $session->get("criteres_liste_taxe")['motcle']
                ]);
            } else {
                $data = $taxeRepository->findAll();
            }
        }
        //dd($session->get("criteres"));
        $taxes = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Taxe";
        return $this->render(
            'taxe.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchTaxeForm->createView(),
                'taxes' => $taxes
            ]
        );







        // $session = $request->getSession();
        // $appTitreRubrique = "Taxe";
        // $repository = $doctrine->getRepository(Taxe::class);
        // $data = $repository->findAll();
        // $taxes = $paginatorInterface->paginate($data, $page, $nbre);


        // return $this->render(
        //     'taxe.list.html.twig',
        //     [
        //         'appTitreRubrique' => $appTitreRubrique,
        //         'taxes' => $taxes
        //     ]
        // );
    }





    #[Route('/details/{id<\d+>}', name: 'taxe.details')]
    public function detail(Taxe $taxe = null): Response
    {
        if ($taxe) {
            return $this->render('taxe.details.html.twig', ['taxe' => $taxe]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('taxe.list');
        }
    }






    #[Route('/edit/{id?0}', name: 'taxe.edit')]
    public function edit(Taxe $taxe = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($taxe == null) {
            $appTitreRubrique = "Taxe / Ajout";
            $adjectif = "ajoutée";
            $taxe = new Taxe();
        } else {
            $appTitreRubrique = "Taxe / Edition";
            $adjectif = "modifiée";
        }

        $form = $this->createForm(TaxeFormType::class, $taxe);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($taxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $taxe->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('taxe.list');
        } else {

            return $this->render(
                'taxe.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'taxe.delete')]
    public function delete(Taxe $taxe = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($taxe != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($taxe);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $taxe->getNom() . " vient d'être supprimée avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('taxe.list');
    }
}
