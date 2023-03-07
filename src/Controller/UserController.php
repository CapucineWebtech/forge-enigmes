<?php

namespace App\Controller;

use App\Entity\WineGame;
use App\Repository\WineGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{

    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    #[Route('/compte', name: 'app_compte')]
    public function compte(WineGameRepository $wineGameRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder
            ->select('wg')
            ->from('App\Entity\WineGame', 'wg')
            ->leftJoin('wg.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());

        $winegames = $queryBuilder->getQuery()->getResult();

        return $this->render('compte.html.twig', [
            'winegames' => $winegames,
            'userName' => $user->getUserIdentifier()
        ]);
    }

    #[Route('/winegame/{id}', name: 'app_wineGame')]
    public function winegame(WineGame $wineGame): Response
    {
        $user = $this->getUser();

        if (!$wineGame->getUser()->contains($user)) {
            return $this->redirectToRoute('app_compte');
        }

        return $this->render('wineGame.html.twig', [
            'userName' => $user->getUserIdentifier()
        ]);
    }
}