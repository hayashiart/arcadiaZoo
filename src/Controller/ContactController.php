<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $title = $request->request->get('title');
            $description = $request->request->get('description');

            if ($email && $title && $description) {
                $contact = new Contact();
                $contact->setEmail($email);
                $contact->setTitle($title);
                $contact->setDescription($description);
                $contact->setCreatedAt(new \DateTime());

                $entityManager->persist($contact);
                $entityManager->flush();

                $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            }
        }

        return $this->render('contact/index.html.twig');
    }
}