<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
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
    public function list(Request $request, ManagerRegistry $doctrine, $page, $nbre, PaginatorInterface $paginatorInterface): Response
    {
        $session = $request->getSession();
        $appTitreRubrique = "Contact";
        $repository = $doctrine->getRepository(Contact::class);
        $data = $repository->findAll();
        $contacts = $paginatorInterface->paginate($data, $page, $nbre);


        return $this->render(
            'contact.list.html.twig',
            [
                'appTitreRubrique' => $appTitreRubrique,
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
