<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/client")]
class ClientController extends AbstractController
{

    #[Route('/', name: 'client.list')]
    public function list(Request $request): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Clients";
        //$this->addFlash('success', "Bien venu sur BDM!");

        return $this->render(
            'client.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique
            ]
        );
    }






    #[Route('/edit/{id?0}', name: 'client.edit')]
    public function edit(Client $client = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($client == null) {
            $appTitreRubrique = "Client / Ajout";
            $adjectif = "ajouté";
            $client = new Client();
        } else {
            $appTitreRubrique = "Client / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(ClientFormType::class, $client);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $client->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('client.edit');
        } else {

            return $this->render(
                'client.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'client.delete')]
    public function delete(Client $client = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($client != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $client->getNom() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('client.list');
    }
}
