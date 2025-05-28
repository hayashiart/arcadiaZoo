<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/services/new', name: 'app_service_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $name = $request->request->get('name');
        $description = $request->request->get('description');

        if ($name && $description) {
            $service->setName($name);
            $service->setDescription($description);
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service créé avec succès.');
        } else {
            $this->addFlash('error', 'Nom ou description invalide.');
        }

        return $this->redirectToRoute('app_services');
    }

    #[Route('/services/{id}/edit', name: 'app_service_edit', methods: ['POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');

        if ($name && $description) {
            $service->setName($name);
            $service->setDescription($description);
            $entityManager->flush();
            $this->addFlash('success', 'Service modifié avec succès.');
        } else {
            $this->addFlash('error', 'Nom ou description invalide.');
        }

        return $this->redirectToRoute('app_services');
    }

    #[Route('/services/{id}/delete', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_services');
    }

#[Route('/services/delete-multiple', name: 'app_service_delete_multiple', methods: ['POST'])]
public function deleteMultiple(Request $request, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager, \Psr\Log\LoggerInterface $logger): Response
{
    $ids = $request->request->all()['service_ids'] ?? [];
    $token = $request->request->get('_token');
    $logger->debug('CSRF Token received', ['token' => $token, 'ids' => $ids]);
    if ($this->isCsrfTokenValid('admin_service_delete_multiple', $token)) {
        $logger->debug('CSRF Token validated successfully');
        foreach ($ids as $id) {
            $service = $serviceRepository->find($id);
            if ($service) {
                $entityManager->remove($service);
            }
        }
        $entityManager->flush();
        $this->addFlash('success', 'Services supprimés avec succès.');
    } else {
        $logger->error('Invalid CSRF token', ['token' => $token, 'expected' => 'admin_service_delete_multiple']);
        $this->addFlash('error', 'Jeton CSRF invalide.');
    }

    return $this->redirectToRoute('app_services');
}
}