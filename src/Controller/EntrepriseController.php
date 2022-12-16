<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/entreprise")]
class EntrepriseController extends AbstractController
{
    #[Route('/list', name: 'entreprise.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Entreprise / List";
        $this->addFlash('success', "Bien venu sur BDM!");
        
        return $this->render('entreprise/entreprise.edit.html.twig', 
        [
            'appTitreRubrique' => $appTitreRubrique
        ]);
    }

    #[Route('/edit', name: 'entreprise.edit')]
    public function edit(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entreprise = new Entreprise();
        $entreprise->setNom("AIB RDC SARL");
        $entreprise->setTelephone("+243828727706");
        $entreprise->setAdresse("Ave de la Gombe - Kinshasa / RDC");
        $entreprise->setIdnat("IDNAT001245");
        $entreprise->setNumipot("NUIMPO454578");
        $entreprise->setRccm("RCCM457878-10/CDK");
        //ajout de l'entreprise dans la transaction
        $entityManager->persist($entreprise);
        //On écrit le SQL dans la base de données
        $entityManager->flush();


        $appTitreRubrique = "Entreprise / Edit";
        $this->addFlash('success', "Bravo !" . $entreprise->getNom() . " vient d'être ajoutée dans la base de données.");
        
        return $this->render('entreprise/entreprise.edit.html.twig', 
        [
            'appTitreRubrique' => $appTitreRubrique
        ]);
    }

    #[Route('/delete', name: 'entreprise.delete')]
    public function delete(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Entreprise / Delete";
        $this->addFlash('success', "Bien venu sur BDM!");
        
        return $this->render('entreprise/entreprise.edit.html.twig', 
        [
            'appTitreRubrique' => $appTitreRubrique
        ]);
    }
}
