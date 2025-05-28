<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\Client;
use Psr\Log\LoggerInterface;

class DashboardController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $animalViews = [];
        try {
            $client = new Client('mongodb://localhost:27017');
            $collection = $client->arcadia->animal_views;
            $animalViews = $collection->find()->toArray();
            $this->logger->info('Successfully retrieved animal views', ['count' => count($animalViews)]);
        } catch (\Exception $e) {
            $this->logger->error('MongoDB error: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur MongoDB : ' . $e->getMessage());
        }

        return $this->render('dashboard/index.html.twig', [
            'animal_views' => $animalViews,
        ]);
    }
}