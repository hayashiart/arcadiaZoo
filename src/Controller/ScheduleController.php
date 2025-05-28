<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    #[Route('/schedule', name: 'app_schedule')]
    public function index(ScheduleRepository $scheduleRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_home');
        }

        $schedules = $scheduleRepository->findAll();
        return $this->render('schedule/index.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/schedule/new', name: 'app_schedule_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_schedule');
        }

        $day = $request->request->get('day');
        $openingTime = $request->request->get('opening_time');
        $closingTime = $request->request->get('closing_time');
        $isClosed = $request->request->get('is_closed') === 'on';

        if ($day) {
            $schedule = new Schedule();
            $schedule->setDay($day);
            $schedule->setOpeningTime($openingTime ? new \DateTime($openingTime) : null);
            $schedule->setClosingTime($closingTime ? new \DateTime($closingTime) : null);
            $schedule->setIsClosed($isClosed);
            $entityManager->persist($schedule);
            $entityManager->flush();
            $this->addFlash('success', 'Horaire ajouté avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_schedule');
    }

    #[Route('/schedule/edit/{id}', name: 'app_schedule_edit', methods: ['POST'])]
    public function edit(Request $request, Schedule $schedule, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_schedule');
        }

        $day = $request->request->get('day');
        $openingTime = $request->request->get('opening_time');
        $closingTime = $request->request->get('closing_time');
        $isClosed = $request->request->get('is_closed') === 'on';

        if ($day) {
            $schedule->setDay($day);
            $schedule->setOpeningTime($openingTime ? new \DateTime($openingTime) : null);
            $schedule->setClosingTime($closingTime ? new \DateTime($closingTime) : null);
            $schedule->setIsClosed($isClosed);
            $entityManager->flush();
            $this->addFlash('success', 'Horaire modifié avec succès.');
        } else {
            $this->addFlash('error', 'Données invalides.');
        }

        return $this->redirectToRoute('app_schedule');
    }

    #[Route('/schedule/delete/{id}', name: 'app_schedule_delete', methods: ['POST'])]
    public function delete(Request $request, Schedule $schedule, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_schedule');
        }

        if ($this->isCsrfTokenValid('delete'.$schedule->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schedule);
            $entityManager->flush();
            $this->addFlash('success', 'Horaire supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_schedule');
    }
}