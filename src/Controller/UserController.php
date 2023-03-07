<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/winegame', name: 'wine_game')]
    public function index(): Response
    {
        if (!($this->getUser())) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        return $this->render('wineGame.html.twig', [
            'userName' => $user->getUserIdentifier()
        ]);
    }
}