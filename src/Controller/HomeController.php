<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findBy(['is_valid' => true]);

        return $this->render('home/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    // Nouvelle route pour soumettre un avis
    #[Route('/review/submit', name: 'app_review_submit', methods: ['POST'])]
    public function submitReview(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pseudo = $request->request->get('pseudo');
        $comment = $request->request->get('comment');

        if ($pseudo && $comment) {
            $review = new Review();
            $review->setPseudo($pseudo);
            $review->setComment($comment);
            $review->setCreatedAt(new \DateTime());
            $review->setIsValid(true); // Validé par défaut

            $entityManager->persist($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}