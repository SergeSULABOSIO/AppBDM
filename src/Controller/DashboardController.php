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


#[Route("/dashboard")]
class DashboardController extends AbstractController
{

    #[Route('/index', name: 'dashboard')]
    public function index(
        Request $request,
        TaxeRepository $taxeRepository,
        PoliceRepository $policeRepository,
        ProduitRepository $produitRepository,
        ClientRepository $clientRepository,
        PartenaireRepository $partenaireRepository,
        AssureurRepository $assureurRepository
    ): Response {
        $agregats_dashboard = new PoliceAgregat();
        $session_name_dashboard = "criteres_liste_dashboard";

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
            $data = $policeRepository->findByMotCle($criteres_dashboard, $agregats_dashboard, $taxes);
            $session_dashboard->set($session_name_dashboard, $criteres_dashboard);
            //dd($session->get("criteres_liste_pop_taxe"));
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
            }
        }

        //dd($data);
        $data_primes_assureur[] = [
            'label' => 'SFA',
            'data' => 850000,
            'color'=> 'blue'
        ];
        $data_primes_assureur[] = [
            'label' => 'RAWSUR',
            'data' => 650000,
            'color'=> 'gray'
        ];
        $data_primes_assureur[] = [
            'label' => 'MAYFAIR',
            'data' => 50000,
            'color'=> 'red'
        ];
        $data_primes_assureur[] = [
            'label' => 'SUNU',
            'data' => 12000,
            'color'=> 'green'
        ];


        $data_com_nettes[] = [
            'label' => 'SFA',
            'data' => 85000,
            'color'=> 'blue'
        ];
        $data_com_nettes[] = [
            'label' => 'RAWSUR',
            'data' => 65000,
            'color'=> 'gray'
        ];
        $data_com_nettes[] = [
            'label' => 'MAYFAIR',
            'data' => 50000,
            'color'=> 'red'
        ];
        $data_com_nettes[] = [
            'label' => 'SUNU',
            'data' => 12000,
            'color'=> 'green'
        ];


        
        $data_com_impayees[] = [
            'label' => 'SFA',
            'data' => 85000,
            'color'=> 'blue'
        ];
        $data_com_impayees[] = [
            'label' => 'RAWSUR',
            'data' => 65000,
            'color'=> 'gray'
        ];
        $data_com_impayees[] = [
            'label' => 'MAYFAIR',
            'data' => 5000,
            'color'=> 'red'
        ];
        $data_com_impayees[] = [
            'label' => 'SUNU',
            'data' => 150,
            'color'=> 'green'
        ];


        //Les primes TTC
        $data_primes_ttc_mois[] = 15000;
        $data_primes_ttc_mois[] = 1500;
        $data_primes_ttc_mois[] = 24500;
        $data_primes_ttc_mois[] = 30000;
        $data_primes_ttc_mois[] = 60000;
        $data_primes_ttc_mois[] = 73500;
        $data_primes_ttc_mois[] = 9500;
        $data_primes_ttc_mois[] = 10000;
        $data_primes_ttc_mois[] = 35000;
        $data_primes_ttc_mois[] = 6550;
        $data_primes_ttc_mois[] = 11500;
        $data_primes_ttc_mois[] = 23000;

        //Les primes nettes ht
        $data_primes_ht_mois[] = 1000;
        $data_primes_ht_mois[] = 1000;
        $data_primes_ht_mois[] = 21500;
        $data_primes_ht_mois[] = 28000;
        $data_primes_ht_mois[] = 52000;
        $data_primes_ht_mois[] = 70500;
        $data_primes_ht_mois[] = 9100;
        $data_primes_ht_mois[] = 8500;
        $data_primes_ht_mois[] = 30000;
        $data_primes_ht_mois[] = 6050;
        $data_primes_ht_mois[] = 9500;
        $data_primes_ht_mois[] = 20000;


        //Les fronting
        $data_fronting_mois[] = 170;
        $data_fronting_mois[] = 170;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 2500;
        $data_fronting_mois[] = 5000;
        $data_fronting_mois[] = 7500;
        $data_fronting_mois[] = 910;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 0;
        $data_fronting_mois[] = 50;
        $data_fronting_mois[] = 150;
        $data_fronting_mois[] = 2000;


        //[1500, 1500, 24500, 30000, 60000, 73500, 9500, 10000, 35000, 6550, 11500, 23000]
        $data_com_nettes_mois[] = 1500;
        $data_com_nettes_mois[] = 1500;
        $data_com_nettes_mois[] = 24500;
        $data_com_nettes_mois[] = 30000;
        $data_com_nettes_mois[] = 60000;
        $data_com_nettes_mois[] = 73500;
        $data_com_nettes_mois[] = 9500;
        $data_com_nettes_mois[] = 10000;
        $data_com_nettes_mois[] = 35000;
        $data_com_nettes_mois[] = 6550;
        $data_com_nettes_mois[] = 11500;
        $data_com_nettes_mois[] = 23000;

        //[15000, 2000, 25000, 35000, 65000, 75000, 10000, 15500, 64000, 6550, 12000, 25000]
        $data_com_encaissees_mois[] = 15000;
        $data_com_encaissees_mois[] = 2000;
        $data_com_encaissees_mois[] = 25000;
        $data_com_encaissees_mois[] = 35000;
        $data_com_encaissees_mois[] = 65000;
        $data_com_encaissees_mois[] = 75000;
        $data_com_encaissees_mois[] = 10000;
        $data_com_encaissees_mois[] = 15500;
        $data_com_encaissees_mois[] = 64000;
        $data_com_encaissees_mois[] = 6550;
        $data_com_encaissees_mois[] = 12000;
        $data_com_encaissees_mois[] = 25000;

        //[13500, 500, 500, 5000, 5000, 1500, 500, 5500, 29000, 0, 500, 2000]
        $data_com_impayees_mois[] = 13500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 5000;
        $data_com_impayees_mois[] = 5000;
        $data_com_impayees_mois[] = 1500;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 5500;
        $data_com_impayees_mois[] = 29000;
        $data_com_impayees_mois[] = 0;
        $data_com_impayees_mois[] = 500;
        $data_com_impayees_mois[] = 2000;

        //dd($data_com_impayees_mois);

        $appTitreRubrique = "Tableau de bord";
        return $this->render(
            'dashboard.html.twig',//'dashboard_test.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $search_Dashboard_Form->createView(),
                //les primes par mois
                'data_primes_ttc_mois' => $data_primes_ttc_mois,
                'data_primes_ht_mois' => $data_primes_ht_mois,
                'data_fronting_mois' => $data_fronting_mois,
                //les primes par assureur
                'data_primes_assureur' => $data_primes_assureur,
                //les commissions en général
                'data_com_impayees' => $data_com_impayees,
                'data_com_nettes' => $data_com_nettes,
                //les commissions par mois
                'data_com_nettes_mois' => $data_com_nettes_mois,
                'data_com_encaissees_mois' => $data_com_encaissees_mois,
                'data_com_impayees_mois' => $data_com_impayees_mois,
                //'polices' => $polices,
                'agregats' => $agregats_dashboard
            ]
        );
    }
}
