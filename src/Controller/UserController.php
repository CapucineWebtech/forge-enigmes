<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Devis;
use App\Entity\User;
use App\Entity\WineGame;
use App\Form\NewWineGameType;
use App\Form\UserWineGameType;
use App\Form\WineGameType;
use App\Repository\ContactRepository;
use App\Repository\DevisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    public function compte(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if($this->isGranted('ROLE_MACHINE')) {
            return $this->redirectToRoute('app_index');
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('wg')
            ->from('App\Entity\WineGame', 'wg')
            ->leftJoin('wg.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());

        if ($this->isGranted('ROLE_ADMIN')) {
            $queryBuilder
                ->select('wg1')
                ->from('App\Entity\WineGame', 'wg1');
        }

        $winegames = $queryBuilder->getQuery()->getResult();

        return $this->render('compte.html.twig', [
            'winegames' => $winegames
        ]);
    }

    #[Route('/newWineGame', name: 'app_newWineGame')]
    public function newWineGame(UserRepository $userRepository, Request $request): Response
    {
        if (!($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }

        $wineGame = new WineGame();
        $form = $this->createForm(NewWineGameType::class, $wineGame);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);
            $wineGame->setPadlockIsOpen(0);
            $wineGame->setBottleRing(0);
            $wineGame->addUser($user);
            $this->em->persist($wineGame);
            $this->em->flush();

            $this->addFlash(
                'successWineGame',
                "Wine Game créé"
            );
            return $this->redirectToRoute('app_compte');
        }

        return $this->render('newWineGame.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/winegame/{id}', name: 'app_wineGame')]
    public function winegame(WineGame $wineGame, Request $request): Response
    {
        $user = $this->getUser();
        if (!$wineGame->getUser()->contains($user) && !($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }
        if($this->isGranted('ROLE_MACHINE')) {
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(WineGameType::class, $wineGame);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($wineGame);
            $this->em->flush();
            $this->addFlash(
                'success',
                "Données mises à jour. Pour que les changements sur la bouteille soient effectifs, veuillez la redémarrer."
            );
            return $this->redirectToRoute('app_wineGame', ['id' => $wineGame->getId()]);
        }

        return $this->render('wineGame.html.twig', [
            'form' => $form,
            'wineGame' => $wineGame
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/activateBottle/{id}', name: 'app_activateBottle')]
    public function activateBottle(WineGame $wineGame)
    {
        $wineGame->setBottleRing(1);
        $this->em->persist($wineGame);
        $this->em->flush();
        $this->addFlash(
            'success',
            "La bouteille va sonner."
        );
        return $this->redirectToRoute('app_wineGame', ['id' => $wineGame->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/activatePadlock/{id}', name: 'app_activatePadlock')]
    public function activatePadlock(WineGame $wineGame)
    {
        if($wineGame->isPadlockIsOpen()) {
            $wineGame->setPadlockIsOpen(0);
            $this->addFlash(
                'success',
                "Le cadenas reprend son comportement par défaut"
            );
        }else{
            $wineGame->setPadlockIsOpen(1);
            $this->addFlash(
                'success',
                "Le cadenas s'ouvre"
            );
        }
        $this->em->persist($wineGame);
        $this->em->flush();
        return $this->redirectToRoute('app_wineGame', ['id' => $wineGame->getId()]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(ContactRepository $contactRepository, DevisRepository $devisRepository): Response
    {
        if (!($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }

        $contacts = $contactRepository->findAll();
        $deviss = $devisRepository->findAll();

        return $this->render('admin.html.twig', [
            'contacts' => $contacts,
            'deviss' => $deviss
        ]);
    }

    #[Route('/devis/{id}', name: 'app_devisRead')]
    public function devis(Devis $devis): Response
    {
        if (!($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }
        return $this->render('devis-read.html.twig', [
            'devis' => $devis
        ]);
    }

    #[Route('/user-machine', name: 'app_user-machine')]
    public function userMachine(UserRepository $userRepository, Request $request): Response
    {
        if (!($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }

        $users = $userRepository->findAll();
        $links = array();
        foreach ($users as $user) {
            foreach ($user->getWineGames() as $game) {
                $links[] = array(
                    'user' => $user,
                    'game' => $game
                );
            }
        }

        $form = $this->createForm(UserWineGameType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData();
            $game = $form->get('wineGame')->getData();
            $user->addWineGame($game);
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'successAdd',
                "Le lien a bien été créé"
            );
            return $this->redirectToRoute('app_user-machine');
        }

        return $this->render('user-machine.html.twig', [
            'links' => $links,
            'form' => $form
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/deleteUserWineGame/{user}/{game}', name: 'app_deleteUser-WineGame', methods: ['POST'])]
    public function deleteUserGameLink(User $user, WineGame $game, Request $request): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-user-wineGame'.$user->getId().$game->getId(), $submittedToken)) {
            $user->removeWineGame($game);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_user-machine');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/deleteContact/{id}', name: 'app_deleteContact', methods: ['POST'])]
    public function deleteTask(Contact $contact, Request $request) : RedirectResponse
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-contact'.$contact->getId(), $submittedToken)) {
            $this->em->remove($contact);
            $this->em->flush();
        }

        $this->addFlash(
            'successDelete',
            "Le message a bien été supprimé."
        );
        return $this->redirectToRoute('app_admin');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/deleteDevis/{id}', name: 'app_deleteDevis', methods: ['POST'])]
    public function deleteDevis(Devis $devis, Request $request) : RedirectResponse
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-devis'.$devis->getId(), $submittedToken)) {
            $this->em->remove($devis);
            $this->em->flush();
        }

        $this->addFlash(
            'successDelete',
            "Le devis a bien été supprimé."
        );
        return $this->redirectToRoute('app_admin');
    }
}