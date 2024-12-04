<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route("/user/app/authors/author-list", name: "app_author_list", methods: ['GET'])]
    final public function bookList(AuthorRepository $authorRepository){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $authors = $authorRepository->findAll();

        return $this->render("author/author-list.html.twig",[
            'authors' => $authors
        ]);

    }

    #[Route("/admin/app/authors/add-author", name:"app_authors_add_author", methods: ['GET', 'POST'])]
    final public function addBook(Request $request, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $author = new Author();
        $addAuthorForm = $this->createForm(AuthorType::class, $author);

        $addAuthorForm->handleRequest($request);

        if ($addAuthorForm->isSubmitted() && $addAuthorForm->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();
            return $this->redirectToRoute('app_author_list');
        }

        return $this->render("forms/form-author.html.twig",[
            'author_form' => $addAuthorForm
        ]);

    }

    #[Route("/admin/app/authors/edit-author/{id<\d+>}", name: "app_authors_edit_author", methods: ['GET', 'POST'])]
    final public function editBook(int $id, AuthorRepository $authorRepository, Request $request, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $author = $authorRepository->findBy(["id" => $id])[0];
        $editAuthorForm = $this->createForm(AuthorType::class, $author);

        $editAuthorForm->handleRequest($request);

        if ($editAuthorForm->isSubmitted() && $editAuthorForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_author_list');
        }

        return $this->render("forms/form-author.html.twig",[
            'author_form' => $editAuthorForm
        ]);

    }

    #[Route("/admin/app/authors/delete-author/{id<\d+>}", name: "app_authors_delete_author", methods: ['GET', 'POST'])]
    final public function deleteBook(int $id, AuthorRepository $authorRepository, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $author = $authorRepository->findBy(["id" => $id])[0];
        
        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('app_author_list');

    }
}
