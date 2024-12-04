<?php

namespace App\Controller;

use App\Entity\Borrow;
use App\Form\BorrowType;
use App\Repository\BookRepository;
use App\Repository\BorrowRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Date;

class BorrowController extends AbstractController
{
    #[Route('/user/app/borrows/borrow-list', name: 'app_borrow_list', methods: ['GET'])]
    final public function borrowList(BorrowRepository $borrowRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $borrows = $borrowRepository->findAll();

        return $this->render('borrow/borrow-list.html.twig', [
            'borrows' => $borrows
        ]);
    }

    #[Route('/user/app/borrows/begin-borrow/{idBook<\d+>}/{idUser<\d+>}', name: 'app_borrows_begin_borrow', methods: ['GET', 'POST'])]
    final public function addBorrow(int $idBook, int $idUser, BookRepository $bookRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $book = $bookRepository->findBy(["id" => $idBook])[0];
        $user = $userRepository->findBy(["id" => $idUser])[0];
         
        $borrow = new Borrow();
        $borrow->setBorrowDate(new DateTime());
        $borrow->setStatus("en cours");
        $borrow->setBook($book);
        $borrow->setUser($user);

        $entityManager->persist($borrow);
        $entityManager->flush();
        return $this->redirectToRoute("app_borrow_list");
    }

    #[Route('/user/app/borrows/end-borrow/{id<\d+>}', name: 'app_borrows_end_borrow', methods: ['GET', 'POST'])]
    final public function endBorrow(int $id, BorrowRepository $borrowRepository, Request $request, EntityManagerInterface $entityManager)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $borrow = $borrowRepository->findBy(["id" => $id])[0];
        $borrow->setStatus("terminé");

        $entityManager->flush();
        return $this->redirectToRoute("app_borrow_list");
    }
}
