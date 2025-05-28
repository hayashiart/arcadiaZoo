<?php

namespace App\Controller;

use App\Entity\Feeding;
use App\Repository\FeedingRepository;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedingController extends AbstractController
{
    #[Route('/feeding', name: 'app_feeding')]
    public function index(FeedingRepository $feedingRepository, AnimalRepository $animalRepository): Response
    {
        $feedings = $feedingRepository->findAll();
        $animals = $animalRepository->findAll();
        $animalMap = [];
        foreach ($animals as $animal) {
            $animalMap[$animal->getId()] = $animal->getName();
        }

        return $this->render('feeding/index.html.twig', [
            'feedings' => $feedings,
            'animal_map' => $animalMap,
        ]);
    }

    #[Route('/feeding/new', name: 'app_feeding_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYEE')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_feeding');
        }

        $animalId = $request->request->get('animal_id');
        $food = $request->request->get('food');
        $quantity = $request->request->get('quantity');
        $date = $request->request->get('date');
        $animal = $animalRepository->find($animalId);

        if ($animal && $food && $quantity && $date) {
            $feeding = new Feeding();
            $feeding->setAnimal($animal);
            $feeding->setFood($food);
            $feeding->setQuantity((int)$quantity);
            $feeding->setDate(new \DateTime($date));
            $feeding->setEmployee($this->getUser());
            $entityManager->persist($feeding);
            $entityManager->flush();
            $this->addFlash('success', 'Alimentation ajoutée avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_feeding');
    }

    #[Route('/feeding/edit/{id}', name: 'app_feeding_edit', methods: ['POST'])]
    public function edit(Request $request, Feeding $feeding, EntityManagerInterface $entityManager, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYEE')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_feeding');
        }

        $animalId = $request->request->get('animal_id');
        $food = $request->request->get('food');
        $quantity = $request->request->get('quantity');
        $date = $request->request->get('date');
        $animal = $animalRepository->find($animalId);

        if ($animal && $food && $quantity && $date) {
            $feeding->setAnimal($animal);
            $feeding->setFood($food);
            $feeding->setQuantity((int)$quantity);
            $feeding->setDate(new \DateTime($date));
            $entityManager->flush();
            $this->addFlash('success', 'Alimentation modifiée avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_feeding');
    }

    #[Route('/feeding/delete/{id}', name: 'app_feeding_delete', methods: ['POST'])]
    public function delete(Request $request, Feeding $feeding, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYEE')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_feeding');
        }

        if ($this->isCsrfTokenValid('delete'.$feeding->getId(), $request->request->get('_token'))) {
            $entityManager->remove($feeding);
            $entityManager->flush();
            $this->addFlash('success', 'Alimentation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_feeding');
    }
}