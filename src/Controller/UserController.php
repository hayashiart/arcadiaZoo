<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users/new', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $email = $request->request->get('email');
        $roles = [$request->request->get('role')];
        $password = $request->request->get('password', 'defaultpassword123'); // Mot de passe par défaut

        if ($email && in_array($roles[0], ['ROLE_ADMIN', 'ROLE_VET', 'ROLE_EMPLOYEE'])) {
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setCreatedAt(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');
        } else {
            $this->addFlash('error', 'Email ou rôle invalide.');
        }

        return $this->redirectToRoute('app_users');
    }

    #[Route('/users/{id}/edit', name: 'app_user_edit', methods: ['POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');
        $roles = [$request->request->get('role')];

        if ($email && in_array($roles[0], ['ROLE_ADMIN', 'ROLE_VET', 'ROLE_EMPLOYEE'])) {
            $user->setEmail($email);
            $user->setRoles($roles);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
        } else {
            $this->addFlash('error', 'Email ou rôle invalide.');
        }

        return $this->redirectToRoute('app_users');
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_users');
    }

#[Route('/users/delete-multiple', name: 'app_user_delete_multiple', methods: ['POST'])]
public function deleteMultiple(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    $ids = $request->request->all()['user_ids'] ?? [];
    if ($this->isCsrfTokenValid('delete_multiple', $request->request->get('_token'))) {
        foreach ($ids as $id) {
            $user = $userRepository->find($id);
            if ($user) {
                $entityManager->remove($user);
            }
        }
        $entityManager->flush();
        $this->addFlash('success', 'Utilisateurs supprimés avec succès.');
    } else {
        $this->addFlash('error', 'Jeton CSRF invalide.');
    }

    return $this->redirectToRoute('app_users');
}
}