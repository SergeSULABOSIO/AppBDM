<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Produit;
use App\Form\ClientFormType;
use App\Repository\ClientRepository;
use ClientSearchType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/client")]
class ClientController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'client.list')]
    public function search(Request $request, $page, $nbre, ClientRepository $clientRepository, PaginatorInterface $paginatorInterface)
    {
        $searchClientForm = $this->createForm(ClientSearchType::class);
        $searchClientForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchClientForm->isSubmitted() && $searchClientForm->isValid()) {
            $page = 1;
            $criteres = $searchClientForm->getData();
            $data = $clientRepository->findByMotCle($criteres);
            $session->set("criteres_liste_client", $criteres);
        }else{
            if($session->has("criteres_liste_client")){
                $data = $clientRepository->findByMotCle($session->get("criteres_liste_client"));
                $criteres = new ClientSearchType();
                $searchClientForm = $this->createForm(ClientSearchType::class, [
                    'motcle' => $session->get("criteres_liste_client")['motcle'],
                    'secteur' => $session->get("criteres_liste_client")['secteur']
                ]);
            }else{
                $data = $clientRepository->findAll();
            }
        }
        //dd($session->get("criteres"));
        $clients = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Client";
        return $this->render(
            'client.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchClientForm->createView(),
                'clients' => $clients
            ]
        );
    }




    #[Route('/details/{id<\d+>}', name: 'client.details')]
    public function detail(Client $client = null): Response
    {
        if ($client) {
            return $this->render('client.details.html.twig', ['client' => $client]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('client.list');
        }
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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $client->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('client.list');
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
