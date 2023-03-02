<?php

namespace App\Controller;

use DateTime;
use PoliceSearchType;
use App\Agregats\PoliceAgregat;
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
use App\Repository\PaiementCommissionRepository;
use App\Repository\PaiementPartenaireRepository;
use App\Repository\PaiementTaxeRepository;
use App\Repository\PartenaireRepository;
use App\Repository\TaxeRepository;
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
        OutstandingCommissionRepository $outstandingCommissionRepository,
        PaiementCommissionRepository $paiementCommissionRepository,
        PaiementPartenaireRepository $paiementPartenaireRepository,
        PaiementTaxeRepository $paiementTaxeRepository,
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
        } else {
            //dd($session->get("criteres_liste_pop_taxe"));
            $criteres_dashboard = $session_dashboard->get($session_name_dashboard);
            if ($criteres_dashboard) {
                $session_produit = $criteres_dashboard['produit'] ? $criteres_dashboard['produit'] : null;
                $session_partenaire = $criteres_dashboard['partenaire'] ? $criteres_dashboard['partenaire'] : null;
                $session_client = $criteres_dashboard['client'] ? $criteres_dashboard['client'] : null;
                $session_assureur = $criteres_dashboard['assureur'] ? $criteres_dashboard['assureur'] : null;

                $objproduit = $session_produit ? $produitRepository->find($session_produit->getId()) : null;
                $objPartenaire = $session_partenaire ? $partenaireRepository->find($session_partenaire->getId()) : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                $objAssureur = $session_assureur ? $assureurRepository->find($session_assureur->getId()) : null;

                $data_police = $policeRepository->findByMotCle($criteres_dashboard, $agregats_dashboard, $taxes);

                $search_Dashboard_Form = $this->createForm(PoliceSearchType::class, [
                    'motcle' => $criteres_dashboard['motcle'],
                    'produit' => $objproduit,
                    'partenaire' => $objPartenaire,
                    'client' => $objClient,
                    'assureur' => $objAssureur,
                    'dateA' => $criteres_dashboard['dateA'],
                    'dateB' => $criteres_dashboard['dateB']
                ]);
            }else{
                $criteres_dashboard = $search_Dashboard_Form->getData();
                $criteres_dashboard['assureur'] = null;
                $criteres_dashboard['produit'] = null;
                $criteres_dashboard['client'] = null;
                $criteres_dashboard['partenaire'] = null;
                $criteres_dashboard['motcle'] = null;
            }
        }

        


        $tableau_de_bord = new TableauDeBord(
            $paiementCommissionRepository,
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
            $paiementPartenaireRepository,
            $paiementTaxeRepository,
            $data_police,
            $criteres_dashboard
        );
        //dd($tableau_de_bord);

        //dd($tableau_de_bord->dash_get_synthse_retrocommissoins_mois());

        $appTitreRubrique = "Tableau de bord";
        return $this->render(
            'dashboard.html.twig',//'dashboard_test.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $search_Dashboard_Form->createView(),
                //les primes par mois
                'data_taxes' => $taxes,
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
                //les synthèses de production & taxes
                'data_syntheses_production_assureur' => $tableau_de_bord->dash_get_synthse_production_assureur(),
                'data_syntheses_production_mois' => $tableau_de_bord->dash_get_synthse_production_mois(),
                'data_syntheses_retrocommissions_mois' => $tableau_de_bord->dash_get_synthse_retrocommissoins_mois(),
                'data_syntheses_impots_et_taxes_mois' => $tableau_de_bord->dash_get_synthse_impots_et_taxes_mois(),
                //'polices' => $polices,
                'agregats' => $agregats_dashboard
            ]
        );
    }
}
