<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            return $this->render('index.html.twig', [
                'userName' => $user->getUserIdentifier()
            ]);
        }
        return $this->render('index.html.twig');
    }
}