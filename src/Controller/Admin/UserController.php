<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/role', name: 'admin_user_role', methods: ['POST'])]
    public function changeRole(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $role = $request->request->get('role');

        if (!in_array($role, ['ROLE_USER', 'ROLE_ADMIN'])) {
            $this->addFlash('error', 'Rôle invalide.');
            return $this->redirectToRoute('admin_user_index');
        }

        $user->setRoles([$role]);
        $em->flush();

        $this->addFlash('success', "Le rôle de {$user->getEmail()} a été mis à jour.");

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($user === $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas supprimer votre propre compte.");
            return $this->redirectToRoute('admin_user_index');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur {$user->getEmail()} a été supprimé.");
        } else {
            $this->addFlash('error', "Token CSRF invalide.");
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
