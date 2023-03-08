<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Devis;
use App\Entity\WineGame;
use App\Repository\ContactRepository;
use App\Repository\DevisRepository;
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

    #[Route('/winegame/{id}', name: 'app_wineGame')]
    public function winegame(WineGame $wineGame): Response
    {
        $user = $this->getUser();

        if (!$wineGame->getUser()->contains($user) && !($this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_compte');
        }

        return $this->render('wineGame.html.twig');
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
            "Le message à bien été supprimée."
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
            "Le devis à bien été supprimée."
        );
        return $this->redirectToRoute('app_admin');
    }
}