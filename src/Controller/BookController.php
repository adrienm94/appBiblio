<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {

    #[Route("/user/app/books/book-list", name: "app_book_list", methods: ['GET'])]
    final public function bookList(UserRepository $userRepository, BookRepository $bookRepository){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // on obtient les infos de l'utilisateur connecté
        $connectedUserIdentifier = $this->getUser()->getUserIdentifier(); // email user
        $connectedUser = $userRepository->findBy(["email" => $connectedUserIdentifier])[0];

        $books = $bookRepository->findAll();

        return $this->render("book/book-list.html.twig",[
            'books' => $books,
            'user' => $connectedUser
        ]);

    }

    #[Route("/admin/app/books/add-book", name:"app_books_add_book", methods: ['GET', 'POST'])]
    final public function addBook(Request $request, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $book = new Book();
        $addBookForm = $this->createForm(BookType::class, $book);

        $addBookForm->handleRequest($request);

        if ($addBookForm->isSubmitted() && $addBookForm->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render("forms/form-book.html.twig",[
            'book_form' => $addBookForm
        ]);

    }

    #[Route("/admin/app/books/edit-book/{id<\d+>}", name: "app_books_edit_book", methods: ['GET', 'POST'])]
    final public function editBook(int $id, BookRepository $bookRepository, Request $request, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $book = $bookRepository->findBy(["id" => $id])[0];
        $editBookForm = $this->createForm(BookType::class, $book);

        $editBookForm->handleRequest($request);

        if ($editBookForm->isSubmitted() && $editBookForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render("forms/form-book.html.twig",[
            'book_form' => $editBookForm
        ]);

    }

    #[Route("/admin/app/books/delete-book/{id<\d+>}", name: "app_books_delete_book", methods: ['GET', 'POST'])]
    final public function deleteBook(int $id, BookRepository $bookRepository, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $book = $bookRepository->findBy(["id" => $id])[0];

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_book_list');

    }

}
