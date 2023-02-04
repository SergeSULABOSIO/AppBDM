<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Entity\Police;
use App\Entity\Entreprise;
use App\Form\PoliceFormType;
use App\Agregats\PoliceAgregat;
use App\Agregats\PoliceAgregats;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Repository\PartenaireRepository;
use App\Repository\TaxeRepository;
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
    public function list(
        Request $request, 
        $page, 
        $nbre, 
        PoliceRepository $policeRepository, 
        ProduitRepository $produitRepository, 
        ClientRepository $clientRepository, 
        PartenaireRepository $partenaireRepository,
        AssureurRepository $assureurRepository,
        TaxeRepository $taxeRepository,
        PaginatorInterface $paginatorInterface
    ): Response
    {
        $agregats = new PoliceAgregat();
        
        $searchPoliceForm = $this->createForm(PoliceSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);
        $searchPoliceForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchPoliceForm->getData();

        $taxes = $taxeRepository->findAll();

        $data = [];
        if ($searchPoliceForm->isSubmitted() && $searchPoliceForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $policeRepository->findByMotCle($criteres, $agregats, $taxes);
            $session->set("criteres_liste_police", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get("criteres_liste_police");
            if ($objCritereSession) {
                $session_produit = $objCritereSession['produit'] ? $objCritereSession['produit'] : null;
                $session_partenaire = $objCritereSession['partenaire'] ? $objCritereSession['partenaire'] : null;
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $session_assureur = $objCritereSession['assureur'] ? $objCritereSession['assureur'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;

                $data = $policeRepository->findByMotCle($objCritereSession, $agregats, $taxes);

                $searchPoliceForm = $this->createForm(PoliceSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'produit' => $objproduit,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
                ]);
            }
        }
        //dd($session->get("criteres"));
        $polices = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Polices";
        return $this->render(
            'police.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchPoliceForm->createView(),
                'polices' => $polices,
                'agregats' => $agregats
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
