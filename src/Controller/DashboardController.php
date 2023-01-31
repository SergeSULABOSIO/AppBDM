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
        PoliceRepository $policeRepository
    ): Response
    {
        
        $appTitreRubrique = "Tableau de bord";
        return $this->render(
            'dashboard.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }

}
