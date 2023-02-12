<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use ContactSearchType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route("/contact")]
class ContactController extends AbstractController
{

    #[Route('/list/{page?1}/{nbre?20}', name: 'contact.list')]
    public function list(Request $request, $page, $nbre, ClientRepository $clientRepository, ContactRepository $contactRepository, PaginatorInterface $paginatorInterface): Response
    {
        $searchContactForm = $this->createForm(ContactSearchType::class);
        $searchContactForm->handleRequest($request);
        $session = $request->getSession();

        $data = [];
        if ($searchContactForm->isSubmitted() && $searchContactForm->isValid()) {
            $page = 1;
            $criteres = $searchContactForm->getData();
            //dd($criteres);
            $data = $contactRepository->findByMotCle($criteres);
            $session->set("criteres_liste_contact", $criteres);
        } else {
            $objCritereSession = $session->get("criteres_liste_contact");
            if ($objCritereSession) {
                $data = $contactRepository->findByMotCle($session->get("criteres_liste_contact"));
                $session_client = $objCritereSession['client'] ? $objCritereSession['client'] : null;
                $objClient = $session_client ? $clientRepository->find($session_client->getId()) : null;
                //$client = $clientRepository->find($session->get("criteres_liste_contact")['client']->getId());
                //dd($session->get("criteres_liste_contact")['client']->getNom());
                
                $searchContactForm = $this->createForm(ContactSearchType::class, [
                   'motcle' => $session->get("criteres_liste_contact")['motcle'],
                   'client' => $objClient
                ]);
            } else {
                $data = $contactRepository->findAll();
            }
        }
        //dd($session->get("criteres"));
        $contacts = $paginatorInterface->paginate($data, $page, $nbre);
        //dd($clients);
        $appTitreRubrique = "Contact";
        return $this->render(
            'contact.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
                'search_form' => $searchContactForm->createView(),
                'contacts' => $contacts
            ]
        );
    }



    #[Route('/details/{id<\d+>}', name: 'contact.details')]
    public function detail(Contact $contact = null): Response
    {
        if ($contact) {
            return $this->render('contact.details.html.twig', ['contact' => $contact]);
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement est introuvable.");
            return $this->redirectToRoute('contact.list');
        }
    }






    #[Route('/edit/{id?0}', name: 'contact.edit')]
    public function edit(Contact $contact = null, ManagerRegistry $doctrine, Request $request): Response
    {

        $appTitreRubrique = "";
        $adjectif = "";
        if ($contact == null) {
            $appTitreRubrique = "Contact / Ajout";
            $adjectif = "ajouté";
            $contact = new Contact();
        } else {
            $appTitreRubrique = "Contact / Edition";
            $adjectif = "modifié";
        }

        $form = $this->createForm(ContactFormType::class, $contact);
        //vérifions le contenu de l'objet requete
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $contact->getNom() . " vient d'être " . $adjectif . " avec succès.");
            return $this->redirectToRoute('contact.list');
        } else {

            return $this->render(
                'contact.edit.html.twig',
                [
                    'appTitreRubrique' => $appTitreRubrique,
                    'form' => $form->createView()
                ]
            );
        }
    }






    #[Route('/delete/{id?0}', name: 'contact.delete')]
    public function delete(Contact $contact = null, ManagerRegistry $doctrine, Request $request): Response
    {
        if ($contact != null) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();
            $this->addFlash('success', "Bravo ! " . $contact->getNom() . " vient d'être supprimé avec succès.");
        } else {
            $this->addFlash('error', "Désolé. Cet enregistrement n'existe pas.");
        }
        return $this->redirectToRoute('contact.list');
    }
}
