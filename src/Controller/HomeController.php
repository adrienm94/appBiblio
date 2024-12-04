<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    final public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute("app_book_list");
        } else {
            return $this->redirectToRoute("app_login");
        }

        
    }
}
