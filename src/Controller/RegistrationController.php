<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{

    #[Route('/admin/app/users/user-list', name: 'app_user_list')]
    final public function userList(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // on obtient les infos de l'utilisateur connecté
        $connectedUserIdentifier = $this->getUser()->getUserIdentifier(); // email user
        $connectedUser = $userRepository->findBy(["email" => $connectedUserIdentifier])[0];

        // récupération de tous les utilisateurs
        $users = $userRepository->findAll();

        return $this->render('user/user-list.html.twig', [
            'users' => $users,
            'connectedUser' => $connectedUser
        ]);
    }

    #[Route('/admin/app/users/register-user', name: 'app_users_register_user')]
    final public function registerUser(UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('registration/register.html.twig', [
            'registration_form' => $form,
            'register_or_edit' => 'register'
        ]);
    }

    #[Route('/admin/app/users/edit-user/{id<\d+>}', name: 'app_users_edit_user')]
    final public function editUser(int $id, UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // utilisateur que l'on modifie
        $user = $userRepository->findBy(["id" => $id])[0];
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('registration/register.html.twig', [
            'registration_form' => $form,
            'register_or_edit' => 'edit'
        ]);

        
    }

    #[Route("/admin/app/users/delete-user/{id<\d+>}", name: "app_users_delete_user", methods: ['GET', 'POST'])]
    final public function deleteUser(int $id, UserRepository $authorRepository, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $author = $authorRepository->findBy(["id" => $id])[0];
        
        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_list');

    }
}
