<?php

namespace App\Controller;

use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    #[Route('/report', name: 'app_report')]
    public function index(Request $request, ReportRepository $reportRepository, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_home');
        }

        $animalId = $request->query->get('animal_id');
        $date = $request->query->get('date');
        $queryBuilder = $reportRepository->createQueryBuilder('r');

        if ($animalId) {
            $queryBuilder->andWhere('r.animal = :animalId')->setParameter('animalId', $animalId);
        }
        if ($date) {
            $queryBuilder->andWhere('DATE(r.visit_date) = :date')->setParameter('date', $date);
        }

        $reports = $queryBuilder->getQuery()->getResult();
        $animals = $animalRepository->findAll();
        $animalMap = [];
        foreach ($animals as $animal) {
            $animalMap[$animal->getId()] = $animal->getName();
        }

        return $this->render('report/index.html.twig', [
            'reports' => $reports,
            'animal_map' => $animalMap,
            'animal_id' => $animalId,
            'date' => $date,
        ]);
    }

    #[Route('/report/new', name: 'app_report_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_report');
        }

        $animalId = $request->request->get('animal_id');
        $status = $request->request->get('status');
        $food = $request->request->get('food');
        $foodQuantity = $request->request->get('food_quantity');
        $visitDate = $request->request->get('visit_date');
        $details = $request->request->get('details');
        $animal = $animalRepository->find($animalId);

        if ($animal && $status && $food && $foodQuantity && $visitDate) {
            $report = new Report();
            $report->setAnimal($animal);
            $report->setStatus($status);
            $report->setFood($food);
            $report->setFoodQuantity((int)$foodQuantity);
            $report->setVisitDate(new \DateTime($visitDate));
            $report->setDetails($details);
            $report->setVet($this->getUser());
            $entityManager->persist($report);
            $entityManager->flush();
            $this->addFlash('success', 'Compte-rendu ajouté avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_report');
    }

    #[Route('/report/edit/{id}', name: 'app_report_edit', methods: ['POST'])]
    public function edit(Request $request, Report $report, EntityManagerInterface $entityManager, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_report');
        }

        $animalId = $request->request->get('animal_id');
        $status = $request->request->get('status');
        $food = $request->request->get('food');
        $foodQuantity = $request->request->get('food_quantity');
        $visitDate = $request->request->get('visit_date');
        $details = $request->request->get('details');
        $animal = $animalRepository->find($animalId);

        if ($animal && $status && $food && $foodQuantity && $visitDate) {
            $report->setAnimal($animal);
            $report->setStatus($status);
            $report->setFood($food);
            $report->setFoodQuantity((int)$foodQuantity);
            $report->setVisitDate(new \DateTime($visitDate));
            $report->setDetails($details);
            $entityManager->flush();
            $this->addFlash('success', 'Compte-rendu modifié avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_report');
    }

    #[Route('/report/delete/{id}', name: 'app_report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_VET')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_report');
        }

        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();
            $this->addFlash('success', 'Compte-rendu supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_report');
    }
}