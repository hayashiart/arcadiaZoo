<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HabitatController extends AbstractController
{
    #[Route('/habitats', name: 'app_habitats')]
    public function index(HabitatRepository $repository): Response
    {
        return $this->render('habitat/index.html.twig', [
            'habitats' => $repository->findAll(),
        ]);
    }

  #[Route('/habitats/new', name: 'app_habitat_create', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $entityManager): Response
{
    $name = $request->request->get('name');
    $description = $request->request->get('description');
    $images = [];

    // Gérer les fichiers uploadés
    if ($request->files->has('images')) {
        foreach ($request->files->get('images') as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $newFileName = uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move('uploads/habitats', $newFileName);
                    $images[] = '/uploads/habitats/' . $newFileName;
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }
        }
    }

    if ($name && $description) {
        $habitat = new Habitat();
        $habitat->setName($name);
        $habitat->setDescription($description);
        $habitat->setImages($images);
        $entityManager->persist($habitat);
        $entityManager->flush();
        $this->addFlash('success', 'Habitat créé avec succès.');
    } else {
        $this->addFlash('error', 'Nom ou description invalide.');
    }

    return $this->redirectToRoute('app_habitats');
}

#[Route('/habitats/edit/{id}', name: 'app_habitat_edit', methods: ['POST'])]
public function edit(Request $request, Habitat $habitat, EntityManagerInterface $entityManager): Response
{
    $name = $request->request->get('name');
    $description = $request->request->get('description');
    $images = $habitat->getImages(); // Conserver les images existantes

    // Gérer les nouveaux fichiers uploadés
    if ($request->files->has('images')) {
        foreach ($request->files->get('images') as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $newFileName = uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move('uploads/habitats', $newFileName);
                    $images[] = '/uploads/habitats/' . $newFileName;
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }
        }
    }

    if ($name && $description) {
        $habitat->setName($name);
        $habitat->setDescription($description);
        $habitat->setImages($images);
        $entityManager->flush();
        $this->addFlash('success', 'Habitat modifié avec succès.');
    } else {
        $this->addFlash('error', 'Nom ou description invalide.');
    }

    return $this->redirectToRoute('app_habitats');
}

    #[Route('/habitats/delete/{id}', name: 'app_habitat_delete', methods: ['POST'])]
    public function delete(Request $request, Habitat $habitat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$habitat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($habitat);
            $entityManager->flush();
            $this->addFlash('success', 'Habitat supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_habitats');
    }

    #[Route('/habitats/delete-multiple', name: 'app_habitat_delete_multiple', methods: ['POST'])]
    public function deleteMultiple(Request $request, HabitatRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $ids = $request->request->all()['habitat_ids'] ?? [];
        if ($this->isCsrfTokenValid('admin_habitat_delete_multiple', $request->request->get('_token'))) {
            foreach ($ids as $id) {
                $habitat = $repository->find($id);
                if ($habitat) {
                    $entityManager->remove($habitat);
                }
            }
            $entityManager->flush();
            $this->addFlash('success', 'Habitats supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_habitats');
    }
}