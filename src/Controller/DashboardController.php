<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Entity\Police;
use App\Entity\Entreprise;
use App\Form\PoliceFormType;
use App\Agregats\PoliceAgregat;
use App\Agregats\PoliceAgregats;
use App\Agregats\TableauDeBord;
use App\Repository\ClientRepository;
use App\Repository\PoliceRepository;
use App\Repository\ProduitRepository;
use App\Repository\AssureurRepository;
use App\Repository\AutomobileRepository;
use App\Repository\ContactRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\MonnaieRepository;
use App\Repository\OutstandingCommissionRepository;
use App\Repository\PartenaireRepository;
use App\Repository\TaxeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/dashboard")]
class DashboardController extends AbstractController
{

    #[Route('/index', name: 'dashboard')]
    public function index(
        Request $request,
        AssureurRepository $assureurRepository,
        AutomobileRepository $automobileRepository,
        ClientRepository $clientRepository,
        ContactRepository $contactRepository,
        EntrepriseRepository $entrepriseRepository,
        TaxeRepository $taxeRepository,
        MonnaieRepository $monnaieRepository,
        PartenaireRepository $partenaireRepository,
        PoliceRepository $policeRepository,
        ProduitRepository $produitRepository,
        OutstandingCommissionRepository $outstandingCommissionRepository
    ): Response {
        $agregats_dashboard = new PoliceAgregat();
        $session_name_dashboard = "criteres_liste_dashboard";
        $tableau_de_bord = null;
        $search_Dashboard_Form = $this->createForm(PoliceSearchType::class, [
            'dateA' => new DateTime('now'),
            'dateB' => new DateTime('now')
        ]);
        $search_Dashboard_Form->handleRequest($request);
        $session_dashboard = $request->getSession();
        $criteres_dashboard = $search_Dashboard_Form->getData();
        $taxes = $taxeRepository->findAll();

        $data_police = [];
        if ($search_Dashboard_Form->isSubmitted() && $search_Dashboard_Form->isValid()) {
            //dd($criteres);
            $data_police = $policeRepository->findByMotCle($criteres_dashboard, $agregats_dashboard, $taxes);
            $session_dashboard->set($session_name_dashboard, $criteres_dashboard);
            //dd($session->get("criteres_liste_pop_taxe"));
            //dd($data_police);
            $tableau_de_bord = new TableauDeBord(
                $assureurRepository,
                $automobileRepository,
                $clientRepository,
                $contactRepository,
                $entrepriseRepository,
                $taxeRepository,
                $monnaieRepository,
                $partenaireRepository,
                $policeRepository,
                $produitRepository,
                $outstandingCommissionRepository,
                $data_police,
                $criteres_dashboard
            );
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $objCritereSession = $session_dashboard->get($session_name_dashboard);
            if ($objCritereSession) {
                $session_produit = $objCritereSession['produit'] ? $objCritereSession['produit'] : null;
                $session_partenaire = $objCritereSession['partenaire'] ? $objCritereSession['partenaire'] : null;
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $session_assureur = $objCritereSession['assureur'] ? $objCritereSession['assureur'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;

                $data_police = $policeRepository->findByMotCle($objCritereSession, $agregats_dashboard, $taxes);

                $search_Dashboard_Form = $this->createForm(PoliceSearchType::class, [
                    'motcle' => $objCritereSession['motcle'],
                    'produit' => $objproduit,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $objCritereSession['dateA'],
                    'dateB' => $objCritereSession['dateB']
                ]);


                $tableau_de_bord = new TableauDeBord(
                    $assureurRepository,
                    $automobileRepository,
                    $clientRepository,
                    $contactRepository,
                    $entrepriseRepository,
                    $taxeRepository,
                    $monnaieRepository,
                    $partenaireRepository,
                    $policeRepository,
                    $produitRepository,
                    $outstandingCommissionRepository,
                    $data_police,
                    $objCritereSession
                );
            }
        }

        


        //dd($data_com_impayees_mois);

        $appTitreRubrique = "Tableau de bord";
        return $this->render(
            'dashboard.html.twig',//'dashboard_test.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $search_Dashboard_Form->createView(),
                //les primes par mois
                'data_primes_ttc_mois' => $tableau_de_bord->dash_get_graphique_primes_ttc_mois(),
                'data_primes_ht_mois' => $tableau_de_bord->dash_get_graphique_primes_ht_mois(),
                'data_fronting_mois' => $tableau_de_bord->dash_get_graphique_fronting_mois(),
                //les primes par assureur
                'data_primes_assureur' => $tableau_de_bord->dash_get_graphique_primes_assureur(),
                //les commissions en général
                'data_com_impayees' => $tableau_de_bord->dash_get_graphique_commissions_impayees_assureur(),
                'data_com_nettes' => $tableau_de_bord->dash_get_graphique_commissions_nettes_assureur(),
                //les commissions par mois
                'data_com_nettes_mois' => $tableau_de_bord->dash_get_graphique_commissions_nettes_mois(),
                'data_com_encaissees_mois' => $tableau_de_bord->dash_get_graphique_commissions_encaissees_mois(),
                'data_com_impayees_mois' => $tableau_de_bord->dash_get_graphique_commissions_impayees_mois(),
                'data_nb_enregistrements' => $tableau_de_bord->dash_get_nb_enregistrements(),
                //'polices' => $polices,
                'agregats' => $agregats_dashboard
            ]
        );
    }
}
