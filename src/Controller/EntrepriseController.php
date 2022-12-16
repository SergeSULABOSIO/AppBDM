<?php

namespace App\Controller;

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
    public function edit(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Entreprise / Edit";
        $this->addFlash('success', "Bien venu sur BDM!");
        
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
