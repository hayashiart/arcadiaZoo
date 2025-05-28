<?php

namespace App\Controller;

use App\Entity\HabitatComment;
use App\Repository\HabitatCommentRepository;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HabitatCommentController extends AbstractController
{
    #[Route('/habitat-comments', name: 'app_habitat_comments')]
    public function index(HabitatCommentRepository $commentRepository, HabitatRepository $habitatRepository): Response
    {
        $comments = $commentRepository->findAll();
        $habitats = $habitatRepository->findAll();
        $habitatMap = [];
        foreach ($habitats as $habitat) {
            $habitatMap[$habitat->getId()] = $habitat->getName();
        }

        return $this->render('habitat_comment/index.html.twig', [
            'comments' => $comments,
            'habitat_map' => $habitatMap,
        ]);
    }

   #[Route('/habitat-comments/new', name: 'app_habitat_comment_new', methods: ['POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, HabitatRepository $habitatRepository): Response
{
    if (!$this->isGranted('ROLE_VET')) {
        $this->addFlash('error', 'Accès refusé.');
        return $this->redirectToRoute('app_habitat_comments');
    }

    $habitatId = $request->request->get('habitat_id');
    $commentText = $request->request->get('comment');
    $habitat = $habitatRepository->find($habitatId);

    if ($habitat && $commentText) {
        $habitatComment = new HabitatComment();
        $habitatComment->setHabitat($habitat);
        $habitatComment->setComment($commentText);
        $habitatComment->setCreatedAt(new \DateTime());
        $habitatComment->setVet($this->getUser());
        $entityManager->persist($habitatComment);
        $entityManager->flush();
        $this->addFlash('success', 'Commentaire ajouté avec succès.');
    } else {
        $this->addFlash('error', 'Habitat ou commentaire invalide.');
    }

    return $this->redirectToRoute('app_habitat_comments');
}
    #[Route('/habitat-comments/edit/{id}', name: 'app_habitat_comment_edit', methods: ['POST'])]
    public function edit(Request $request, HabitatComment $comment, EntityManagerInterface $entityManager, HabitatRepository $habitatRepository): Response
    {
        if (!$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_habitat_comments');
        }

        $habitatId = $request->request->get('habitat_id');
        $commentText = $request->request->get('comment');
        $habitat = $habitatRepository->find($habitatId);

        if ($habitat && $commentText) {
            $comment->setHabitat($habitat);
            $comment->setComment($commentText);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire modifié avec succès.');
        } else {
            $this->addFlash('error', 'Habitat ou commentaire invalide.');
        }

        return $this->redirectToRoute('app_habitat_comments');
    }

    #[Route('/habitat-comments/delete/{id}', name: 'app_habitat_comment_delete', methods: ['POST'])]
    public function delete(Request $request, HabitatComment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_habitat_comments');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_habitat_comments');
    }
}