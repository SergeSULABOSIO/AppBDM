<?php

namespace App\Controller;

use DateTime;
use App\Entity\Client;
use PaiementCommissionSearchType;
use PaiementPartenaireSearchType;
use App\Entity\PaiementCommission;
use App\Repository\PoliceRepository;
use App\Agregats\PopCommissionAgregat;
use App\Form\PaiementCommissionFormType;
use App\Repository\MonnaieRepository;
use App\Repository\PartenaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaiementCommissionRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/popcommission")]
class PaiementCommissionController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'popcommission.list')]
    public function list(Request $request, PoliceRepository $policeRepository, PartenaireRepository $partenaireRepository,  PaiementCommissionRepository $paiementCommissionRepository, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $agregats = new PopCommissionAgregat();

        $searchPaiementCommissionForm = $this->createForm(PaiementCommissionSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);
        $searchPaiementCommissionForm->handleRequest($request);
        $session = $request->getSession();
        $criteres = $searchPaiementCommissionForm->getData();

        $data = [];
        if ($searchPaiementCommissionForm->isSubmitted() && $searchPaiementCommissionForm->isValid()) {
            $page = 1;
            //dd($criteres);
            $data = $paiementCommissionRepository->findByMotCle($criteres, $agregats);
            $session->set("criteres_liste_pop_commission", $criteres);
            //dd($session->get("criteres_liste_pop_taxe"));
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session->get("criteres_liste_pop_commission");
            if ($objCritereSession) {
                $session_police = $objCritereSession['police'] ? $objCritereSession['police'] : null;
                $session_partenaire = $objCritereSession['partenaire'] ? $objCritereSession['partenaire'] : null;
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $session_assureur = $objCritereSession['assureur'] ? $objCritereSession['assureur'] : null;

                $objpolice = $session_police ? $policeRepository->find($session_police->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $partenaireRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $partenaireRepository->find($session_assureur->getId()) : null;

                $data = $paiementCommissionRepository->findByMotCle($objCritereSession, $agregats);

                $searchPaiementCommissionForm = $this->createForm(PaiementCommissionSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'police' => $objpolice,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
                ]);
            }
        }
        //dd($session->get("criteres"));
        $paiementcommissions = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Paiement des commissions";
        return $this->render(
            'paiementcommission.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchPaiementCommissionForm->createView(),
                'paiementcommissions' => $paiementcommissions,
                'agregats' => $agregats
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
        $datePaiement = new DateTime("now");
        if ($popcommission == null) {
            $appTitreRubrique = "Paiement de Commission / Ajout";
            $adjectif = "ajouté";
            $popcommission = new PaiementCommission();
        } else {
            $appTitreRubrique = "Paiement de Commission / Edition";
            $adjectif = "modifié";
            $datePaiement = $popcommission->getDate();
        }

        $form = $this->createForm(PaiementCommissionFormType::class, $popcommission);
        $form->get("date")->setData($datePaiement);
        
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



    #[Route('/deposit/{idpolicy?0}/{amount?0}/{idmonnaie?0}', name: 'popcommission.deposit')]
    public function deposit(
        $idpolicy,
        $idmonnaie,
        $amount,
        PoliceRepository $policeRepository,
        MonnaieRepository $monnaieRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $appTitreRubrique = "";
        $adjectif = "";
        $appTitreRubrique = "Paiement de Commission / Ajout";
        $adjectif = "ajouté";

        $popcommission = new PaiementCommission();
        $police = $policeRepository->find($idpolicy);
        $monnaie = $monnaieRepository->find($idmonnaie);

        if ($police && $monnaie) {
            $popcommission->setMontant($amount);
            $popcommission->setMonnaie($monnaie);
            $popcommission->setPolice($police);
            $popcommission->setDescription("Paiement Commission de Courtage / Police: " . $police->getReference() . " / Client: " . $police->getClient()->getNom());

            //dd($popcommission);

            $form = $this->createForm(PaiementCommissionFormType::class, $popcommission);
            $form->get("date")->setData(new DateTime("now"));
            //vérifions le contenu de l'objet requete
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($popcommission);
                $entityManager->flush();
                $this->addFlash('success', "Bravo ! L'encaissement de " . $monnaie->getCode() . " " . $popcommission->getMontant() . " vient d'être effectué avec succès.");
                return $this->redirectToRoute('outstanding.commission.list');
            } else {
                return $this->render(
                    'paiementcommission.edit.html.twig',
                    [
                        'appTitreRubrique' => $appTitreRubrique,
                        'form' => $form->createView()
                    ]
                );
            }
        } else {
            $this->addFlash('error', "La police et/ou la monnaie n'est pas définie.");
            return $this->redirectToRoute('outstanding.commission.list');
        }
        //dd($monnaie);
        //dd("Amount: " . $amount . " idPolicy: " . $idpolicy);
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
