<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
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

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($contact);
            $this->em->flush();

            $this->addFlash(
                'successContact',
                "Formulaire de contact envoyer"
            );
            return $this->redirectToRoute('app_contact');
        }

        if ($this->getUser()) {
            $user = $this->getUser();
            return $this->render('contact.html.twig', [
                'form' => $form->createView(),
                'userName' => $user->getUserIdentifier()
            ]);
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}