<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController extends AbstractController
{
    #[Route('/animals', name: 'app_animals')]
    public function index(AnimalRepository $animalRepository, HabitatRepository $habitatRepository): Response
    {
        $animals = $animalRepository->findAll();
        $habitats = $habitatRepository->findAll();
        $habitatMap = [];
        foreach ($habitats as $habitat) {
            $habitatMap[$habitat->getId()] = $habitat->getName();
        }

        return $this->render('animal/index.html.twig', [
            'animals' => $animals,
            'habitat_map' => $habitatMap,
        ]);
    }

    #[Route('/animals/new', name: 'app_animal_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, HabitatRepository $habitatRepository): Response
    {
        $name = $request->request->get('name');
        $breed = $request->request->get('breed');
        $habitatId = $request->request->get('habitat_id');
        $habitat = $habitatRepository->find($habitatId);
        $images = [];

        if ($request->files->has('images')) {
            foreach ($request->files->get('images') as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $newFileName = uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move('uploads/animals', $newFileName);
                        $images[] = '/uploads/animals/' . $newFileName;
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                    }
                }
            }
        }

        if ($name && $breed && $habitat) {
            $animal = new Animal();
            $animal->setName($name);
            $animal->setBreed($breed);
            $animal->setHabitat($habitat);
            $animal->setImages($images);
            $entityManager->persist($animal);
            $entityManager->flush();
            $this->addFlash('success', 'Animal créé avec succès.');
        } else {
            $this->addFlash('error', 'Nom, race ou habitat invalide.');
        }

        return $this->redirectToRoute('app_animals');
    }

    #[Route('/animals/edit/{id}', name: 'app_animal_edit', methods: ['POST'])]
public function edit(Request $request, Animal $animal, EntityManagerInterface $entityManager, HabitatRepository $habitatRepository): Response
{
    $name = $request->request->get('name');
    $breed = $request->request->get('breed');
    $habitatId = $request->request->get('habitat_id');
    $habitat = $habitatRepository->find($habitatId);
    $images = $animal->getImages();
    $deleteImages = $request->request->get('delete_images') ? array_filter(explode(',', $request->request->get('delete_images'))) : [];

    // Supprimer les images sélectionnées
    $images = array_filter($images, function($image) use ($deleteImages) {
        return !in_array($image, $deleteImages);
    });

    // Ajouter de nouvelles images
    if ($request->files->has('images')) {
        foreach ($request->files->get('images') as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $newFileName = uniqid() . '.' . $file->guessExtension();
                try {
      $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/animals', $newFileName);
      $images[] = '/uploads/animals/' . $newFileName;
  } catch (FileException $e) {
      $currentUser = posix_getpwuid(posix_geteuid())['name'];
      $targetDir = $this->getParameter('kernel.project_dir') . '/public/uploads/animals';
      $this->addFlash('error', 'Erreur upload : ' . $e->getMessage() . ' | User: ' . $currentUser . ' | Dir writable: ' . (is_writable($targetDir) ? 'Yes' : 'No'));
  }
            } else {
                $this->addFlash('error', 'Fichier invalide ou corrompu.');
            }
        }
    }

    if ($name && $breed && $habitat) {
        $animal->setName($name);
        $animal->setBreed($breed);
        $animal->setHabitat($habitat);
        $animal->setImages(array_values($images));
        $entityManager->flush();
        $this->addFlash('success', 'Animal modifié avec succès.');
    } else {
        $this->addFlash('error', 'Nom, race ou habitat invalide.');
    }

    return $this->redirectToRoute('app_animals');
}

    #[Route('/animals/delete/{id}', name: 'app_animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->request->get('_token'))) {
            $entityManager->remove($animal);
            $entityManager->flush();
            $this->addFlash('success', 'Animal supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_animals');
    }

    #[Route('/animals/delete-multiple', name: 'app_animal_delete_multiple', methods: ['POST'])]
    public function deleteMultiple(Request $request, AnimalRepository $animalRepository, EntityManagerInterface $entityManager): Response
    {
        $ids = $request->request->all()['animal_ids'] ?? [];
        if ($this->isCsrfTokenValid('admin_animal_delete_multiple', $request->request->get('_token'))) {
            foreach ($ids as $id) {
                $animal = $animalRepository->find($id);
                if ($animal) {
                    $entityManager->remove($animal);
                }
            }
            $entityManager->flush();
            $this->addFlash('success', 'Animaux supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_animals');
    }
}